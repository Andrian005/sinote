# PERSONAL OS — TECHNICAL DESIGN DOCUMENT (TDD)
### Acuan Implementasi Teknis — Berdasarkan Blueprint v1.0 & FSD

---

## Prinsip Keputusan Teknis

Setiap keputusan di dokumen ini dipandu oleh tiga batasan yang sudah ditetapkan di Blueprint v1.0: **solo developer**, **modular monolith berbasis Laravel**, dan **fondasi siap tumbuh ke multi-user tanpa perombakan besar**. Bila ada dua pendekatan yang sama-sama valid secara teknis, yang dipilih adalah yang **paling sedikit menambah beban kognitif jangka panjang** bagi satu orang yang merawat sistem ini sendirian.

---

## 1. High Level Architecture

Arsitektur mengikuti model tiga zona dari Blueprint bagian 9: **Presentation** (Blade + Livewire), **Application/Domain** (Action/Service per modul), **Infrastructure** (PostgreSQL, Redis, object storage). TDD ini merinci bagaimana ketiga zona tersebut diimplementasikan secara konkret dalam kode Laravel.

**Alasan:** memisahkan ketiga zona secara tegas sejak awal mencegah logika bisnis "bocor" ke Controller (fat controller) atau ke Model (fat model) — dua anti-pattern paling umum yang menyulitkan solo developer saat aplikasi tumbuh melewati ukuran MVP.

---

## 2. Module Architecture

Setiap modul dari FSD (Inbox, Tasks, Projects & Goals, Tagging/Context, Dashboard, Deadline Reminder, Habit Tracking, Knowledge Base, Focus Mode, Review & Reflection, Full Notification Engine, Search) diimplementasikan sebagai **domain folder mandiri** di dalam `app/Domain/`, bukan sebagai package Composer terpisah.

**Alasan menghindari package terpisah per modul:** memecah menjadi package Composer internal menambah overhead build/autoload yang tidak sepadan untuk solo developer pada skala aplikasi ini; batas modul cukup dijaga secara disiplin lewat konvensi folder dan aturan "satu modul tidak mengakses Model modul lain secara langsung, hanya lewat Action/Event publik miliknya".

---

## 3. Folder Structure

```
app/
├── Domain/
│   ├── Inbox/
│   │   ├── Models/InboxItem.php
│   │   ├── Actions/CaptureInboxItemAction.php
│   │   ├── Actions/TriageInboxItemAction.php
│   │   ├── Enums/InboxItemStatus.php
│   │   └── Events/InboxItemTriaged.php
│   ├── Tasks/
│   │   ├── Models/Task.php
│   │   ├── Actions/CreateTaskAction.php
│   │   ├── Actions/CompleteTaskAction.php
│   │   ├── Enums/TaskStatus.php
│   │   └── Events/TaskCompleted.php
│   ├── Projects/            (mencakup Goal & Project — satu domain, dua entitas terkait erat)
│   ├── Habits/
│   ├── KnowledgeBase/
│   ├── Notification/        (Deadline Reminder + Full Notification Engine)
│   ├── Review/
│   └── Shared/
│       ├── Models/User.php, Tag.php
│       ├── Enums/
│       └── ValueObjects/
├── Http/
│   ├── Controllers/
│   ├── Livewire/            (dikelompokkan mengikuti nama domain, mis. Livewire/Tasks/TaskList.php)
│   ├── Requests/
│   ├── Middleware/
│   └── Resources/           (API Resource, disiapkan sejak awal — lihat bagian 30)
├── Policies/
└── Providers/
```

**Alasan Projects & Goals digabung satu domain:** keduanya memiliki siklus hidup yang saling terkait erat (progres Goal dihitung dari Project) sehingga memisahkannya menjadi dua domain folder hanya menambah indirection tanpa manfaat pemisahan yang nyata.

---

## 4. Laravel Architecture

Aplikasi mengikuti arsitektur Laravel standar (MVC + Service Layer tambahan), **tanpa** framework arsitektur pihak ketiga (mis. package "modular Laravel" dari komunitas) — konvensi Laravel bawaan sudah cukup ekspresif untuk kebutuhan ini, dan menghindari dependency tambahan yang perlu dipelajari dan dirawat solo developer.

Routing tetap terpusat di `routes/web.php`, dikelompokkan per domain menggunakan `Route::prefix()`/`Route::name()` mengikuti nama modul (mis. `Route::prefix('tasks')->name('tasks.')`).

---

## 5. Namespace Convention

```
App\Domain\{Modul}\Models\{Entity}
App\Domain\{Modul}\Actions\{VerbNounAction}
App\Domain\{Modul}\Events\{EntityPastTenseEvent}
App\Domain\{Modul}\Enums\{Entity}{Attribute}
App\Domain\{Modul}\ValueObjects\{Nama}
App\Http\Livewire\{Modul}\{ComponentName}
App\Http\Requests\{Modul}\{Verb}{Entity}Request
App\Policies\{Entity}Policy
```

**Alasan:** namespace mengikuti nama domain (bukan nama teknis generik seperti `App\Services`) agar saat solo developer mencari "di mana logika Task disimpan", jawabannya selalu dapat ditebak dari nama domain tanpa perlu membuka banyak folder.

---

## 6. Service Layer Design

Logika bisnis lintas-Model (mis. menghitung progres Project dari kumpulan Task) ditempatkan di **Action class**, bukan di "Service class" generik bergaya lama (satu `TaskService` besar berisi banyak method tidak berkaitan).

**Alasan menghindari Service class besar:** Service class dengan banyak method berbeda cenderung menjadi "tempat sampah logika" yang sulit ditelusuri seiring waktu — pola Action (satu class = satu operasi bisnis) memberi solo developer titik masuk yang presisi setiap kali menelusuri satu alur kerja tertentu.

---

## 7. Action Pattern

Setiap operasi bisnis penting diimplementasikan sebagai satu class dengan satu method publik `execute()` (atau `__invoke()`), menerima input eksplisit (bukan `Request` langsung, agar dapat dipanggil dari Controller, Job, maupun Command dengan cara yang sama).

Contoh pemetaan operasi bisnis dari FSD ke Action (nama class, bukan kode):
- `CaptureInboxItemAction`, `TriageInboxItemAction`
- `CreateTaskAction`, `CompleteTaskAction`, `ReopenTaskAction`
- `CreateGoalAction`, `CreateProjectAction`, `RecalculateProjectProgressAction`
- `CheckInHabitAction`, `RecalculateHabitStreakAction`
- `ScheduleDeadlineReminderAction`, `SendDueRemindersAction`
- `CreateReviewEntryAction`

**Alasan:** memisahkan "apa yang terjadi" (Action) dari "bagaimana itu dipicu" (Controller/Job/Command) membuat setiap operasi bisnis dapat diuji secara terisolasi (unit test langsung terhadap Action) tanpa perlu mensimulasikan seluruh siklus HTTP request.

---

## 8. DTO Strategy

Data Transfer Object digunakan **secara selektif** — hanya pada Action yang menerima banyak parameter tidak terkait langsung dengan satu Model (mis. `CreateTaskData` berisi judul, deadline, project_id, tag_ids sekaligus), bukan untuk setiap operasi CRUD sederhana.

**Alasan tidak memakai DTO di semua tempat:** untuk operasi CRUD sederhana (mis. `CompleteTaskAction` yang hanya butuh satu `Task` model), DTO justru menambah lapisan tanpa manfaat berarti. DTO baru bernilai saat Action menerima kombinasi data dari berbagai sumber (input form + relasi tag + Project opsional) — di titik itu DTO mencegah signature method Action membengkak dengan banyak parameter individual.

---

## 9. Value Object Recommendation

Direkomendasikan Value Object untuk konsep yang **memiliki aturan/perilaku sendiri**, bukan sekadar tipe data primitif:

- **`StreakCount`** (Modul Habit) — membungkus logika perbandingan dan perhitungan streak, alih-alih integer polos yang logikanya tersebar di banyak tempat.
- **`Priority`** (Modul Tasks) — jika prioritas berkembang lebih kompleks dari sekadar enum (mis. skor gabungan urgensi+kepentingan di masa depan).

**Alasan tidak berlebihan memakai Value Object:** untuk atribut sederhana (judul, deskripsi teks bebas), Value Object hanya menambah boilerplate tanpa manfaat — direkomendasikan hanya dipakai saat ada logika/validasi yang benar-benar melekat pada nilai tersebut.

---

## 10. Enum Strategy

Seluruh status bertingkat dari FSD (State Machine setiap modul) diimplementasikan sebagai **native PHP Enum (backed enum)**, bukan string bebas atau konstanta kelas lama:

- `TaskStatus`: `Todo`, `InProgress`, `Done`, `Archived`
- `InboxItemStatus`: `Unprocessed`, `Processed`, `Discarded`
- `ProjectStatus`: `Active`, `Completed`, `Archived`
- `GoalStatus` (berujung): `Active`, `Completed`, `Abandoned`
- `GoalStatus` (berkelanjutan): `Active`, `Paused`
- `HabitStatus`: `Active`, `Paused`, `Archived`
- `ReminderStatus`: `Scheduled`, `Sent`, `Cancelled`/`Skipped`
- `ReviewPeriodType`: `Daily`, `Weekly`, `Monthly`

**Alasan:** backed enum memberi type-safety penuh di level bahasa (bukan sekadar konvensi string yang mudah salah ketik), dan transisi status yang tidak valid (lihat FSD State Machine tiap modul) dapat divalidasi lewat method pada enum itu sendiri (mis. `TaskStatus::Done->canTransitionTo(TaskStatus::Todo)`).

---

## 11. Repository Pattern (Apakah Diperlukan?)

**Keputusan: Tidak diperlukan.** Eloquent Model dipakai langsung di dalam Action class, tanpa lapisan Repository generik di atasnya.

**Alasan:** Repository Pattern klasik dirancang untuk menyembunyikan detail ORM demi kemungkinan mengganti database/ORM di masa depan — skenario yang **sangat tidak mungkin terjadi** untuk aplikasi ini (Blueprint sudah memfinalkan PostgreSQL + Eloquent). Menambahkan Repository di sini hanya berarti menulis lapisan abstraksi ekstra yang harus dirawat solo developer tanpa manfaat nyata. Eloquent Model *itu sendiri* sudah merupakan bentuk abstraksi data yang cukup.

---

## 12. Event Driven Architecture

Efek samping lintas modul (yang menurut FSD tidak boleh terjadi secara langsung antar-domain) diimplementasikan lewat Laravel Event & Listener, bukan dipanggil langsung dari satu Action ke Action modul lain.

**Alasan:** Event/Listener menjaga batas modul tetap longgar (loosely coupled) — modul Tasks tidak perlu tahu bahwa modul Projects "mendengarkan" penyelesaiannya; ini konsisten dengan prinsip modular monolith di Blueprint bagian 9, dan memudahkan penambahan listener baru di masa depan tanpa mengubah kode Action asal.

---

## 13. Event & Listener Mapping

| Event | Dipicu Oleh | Listener | Efek |
|---|---|---|---|
| `InboxItemTriaged` | `TriageInboxItemAction` | `LogInboxTriageActivity` | Mencatat activity log (FSD 1.2) |
| `TaskCompleted` | `CompleteTaskAction` | `RecalculateProjectProgress` | Update progres Project (FSD 2.2) |
| `TaskCompleted` | `CompleteTaskAction` | `RecordTaskCompletionForReview` | Mencatat riwayat untuk agregasi Review (FSD 2.2, 10.1) |
| `TaskCompleted` | `CompleteTaskAction` | `CancelPendingDeadlineReminder` | Membatalkan reminder aktif untuk Task tsb (FSD 2.2, 6.1) |
| `HabitCheckedIn` | `CheckInHabitAction` | `RecalculateHabitStreak` | Update streak (FSD 7.2) |
| `ProjectCompleted` | Aksi manual user | `RecalculateGoalProgress` | Update progres Goal terkait (FSD 3.1) |
| `NoteLinkedToProject` / `NoteUnlinked` | `LinkNoteToProjectAction` | *(tidak ada listener wajib — hanya relasi langsung)* | — |

**Alasan pemetaan ini eksplisit di dokumen:** memberi solo developer peta lengkap "jika saya ubah Action X, listener apa saja yang perlu ditinjau ulang" — mencegah efek samping tak terduga saat refactoring di kemudian hari.

---

## 14. Notification Architecture

Menggunakan **Laravel Notification** bawaan (bukan pengiriman manual ad-hoc), dengan dua Notification class utama: `DeadlineReminderNotification` dan `HabitOrReviewReminderNotification`, masing-masing mendukung multi-channel (`database` untuk in-app, `mail`/`custom channel` sebagai Future Enhancement sesuai FSD Modul 6 & 11).

**Alasan memakai channel `database`:** Notification dengan channel `database` bawaan Laravel otomatis tersimpan sebagai riwayat yang dapat ditampilkan di Dashboard (FSD Modul 5) tanpa perlu tabel custom terpisah untuk "reminder aktif" — mengurangi duplikasi skema.

---

## 15. Queue Architecture

Seluruh scheduled job dari FSD (pemindaian deadline harian, pemindaian jadwal Habit, reminder ritual Review) diimplementasikan sebagai **Laravel Job** yang di-dispatch ke queue Redis, dipicu oleh **Laravel Scheduler** (`routes/console.php` / Kernel schedule) berjalan harian.

Struktur job mengikuti pola: satu Job "scanner" (mis. `ScanDueDeadlinesJob`) yang berjalan terjadwal, men-dispatch Job pengiriman individual per entitas (mis. `SendDeadlineReminderJob` per Task) — bukan satu Job monolitik yang memproses semua sekaligus secara sinkron.

**Alasan pemisahan scanner vs sender:** jika pengiriman satu reminder gagal (mis. error notifikasi tunggal), kegagalan tersebut tidak menggagalkan pengiriman reminder lain dalam batch yang sama — retry Laravel Queue bekerja per-Job, bukan per-batch.

---

## 16. Cache Strategy

Redis cache diterapkan pada **agregasi Dashboard** (FSD Modul 5) dengan strategi **cache-aside + invalidasi berbasis Event**, bukan time-based expiry murni:

- Saat Dashboard diakses, hasil agregasi (Task prioritas, Habit hari ini) disimpan di cache dengan key `dashboard:{user_id}:{date}`.
- Event `TaskCompleted`, `TaskCreated`, `HabitCheckedIn` memicu Listener yang menghapus (invalidate) cache key terkait, bukan menunggu TTL habis.

**Alasan invalidasi berbasis Event lebih diutamakan dari TTL murni:** Dashboard adalah halaman yang paling sering diakses (Blueprint bagian 17) — data basi di sini paling merusak kepercayaan user terhadap sistem; invalidasi eksplisit menjamin data selalu akurat begitu ada perubahan, sementara TTL hanya berfungsi sebagai jaring pengaman tambahan (fallback) jika suatu invalidasi Event terlewat.

---

## 17. Session Strategy

Session disimpan di **Redis** (bukan file/database) sejak awal, meski aplikasi single-user — konsisten dengan keputusan memakai Redis untuk cache & queue (satu komponen infrastruktur, tiga kegunaan), dan langsung siap untuk skenario multi-instance deployment di masa depan (Blueprint bagian 22) tanpa perlu migrasi driver session belakangan.

---

## 18. Authentication Flow

```
User membuka aplikasi
   → belum terautentikasi → redirect ke /login
   → submit kredensial → Laravel Fortify/Breeze memverifikasi via hashed password
   → sukses → session dibuat, redirect ke /today (Dashboard)
   → gagal → pesan error, rate limiting diterapkan (lihat bagian 27)
```

Sanctum disiapkan berdampingan (bukan menggantikan) session auth, khusus untuk jalur API yang belum aktif penuh (Blueprint bagian 14) — token Sanctum tidak dipakai di alur web utama.

**Alasan mempertahankan session-based auth sebagai jalur utama:** untuk aplikasi server-rendered (Blade+Livewire), session auth lebih sederhana dan lebih aman by default (CSRF protection otomatis) dibanding memaksakan token-based auth yang sebenarnya ditujukan untuk klien stateless (SPA/mobile).

---

## 19. Authorization Flow

```
Request masuk ke Controller/Livewire Action
   → Form Request/method memanggil $this->authorize() atau Gate::allows()
   → Policy method dieksekusi (mis. TaskPolicy::update($user, $task))
   → true  → lanjut ke Action
   → false → HTTP 403, aksi dibatalkan sebelum menyentuh Action/Model
```

Otorisasi **selalu** dicek di titik masuk Controller/Livewire — Action class sendiri **tidak** melakukan pengecekan otorisasi ulang, untuk menjaga satu tanggung jawab jelas (Action fokus pada logika bisnis, bukan keamanan akses).

---

## 20. Policy Design

Satu Policy class per entitas utama (`TaskPolicy`, `ProjectPolicy`, `GoalPolicy`, `HabitPolicy`, `NotePolicy`, `InboxItemPolicy`, `ReviewEntryPolicy`), dengan aturan dasar yang **identik di semua Policy** pada tahap single-user:

```
view/update/delete → true jika $entity->user_id === $user->id, selain itu false
create → true selama $user terautentikasi (tidak ada batasan tambahan di MVP)
```

**Alasan seluruh Policy dibuat penuh sejak awal meski aturannya seragam dan sederhana:** ini adalah keputusan skalabilitas termurah dari Blueprint bagian 12 — saat multi-user/kolaborasi Project (Blueprint bagian 19) diimplementasikan nanti, hanya method Policy yang perlu diperluas (mis. menambah pengecekan kolaborator), tanpa perlu membangun lapisan otorisasi dari nol.

---

## 21. Middleware Recommendation

- `auth` (bawaan) — wajib di seluruh route selain login/register.
- `verified` *(opsional, Future Enhancement)* — jika verifikasi email diaktifkan saat transisi multi-user.
- **Custom middleware `EnsureOwnsResource`** *(opsional, dapat digantikan Policy)* — tidak digunakan karena Policy sudah cukup menangani pengecekan kepemilikan di level method; menambah middleware terpisah untuk hal yang sama hanya duplikasi tanggung jawab.
- `throttle` (bawaan, lihat bagian 27) — pada route autentikasi.

**Alasan minim custom middleware:** middleware sebaiknya dipakai untuk cross-cutting concern yang berlaku di banyak route (auth, throttling), bukan untuk logika otorisasi granular per-entitas yang lebih tepat tinggal di Policy.

---

## 22. Validation Strategy

Seluruh validasi input dilakukan lewat **Form Request class** (satu class per aksi, mis. `StoreTaskRequest`, `UpdateTaskRequest`), bukan validasi inline di Controller/Livewire.

Aturan validasi mengikuti persis **Validation Rules** yang sudah didefinisikan di FSD per fitur (mis. judul Task wajib, panjang teks Inbox 1–5000 karakter, frekuensi Habit 1–7) — FSD menjadi satu-satunya sumber kebenaran aturan bisnis, Form Request hanya menerjemahkannya ke sintaks Laravel.

**Alasan Form Request terpisah per aksi (bukan satu class besar per entitas):** aturan validasi Create dan Update sering berbeda (mis. Update tidak mewajibkan ulang field yang sudah diisi) — memisahkannya mencegah percabangan kondisional yang membingungkan di satu class validasi besar.

---

## 23. Exception Handling Strategy

Custom Exception class dibuat untuk kondisi bisnis spesifik yang disebut eksplisit di FSD (mis. `TaskAlreadyCompletedException`, `DuplicateHabitCheckInException`, `InvalidGoalTypeTransitionException`), ditangkap secara terpusat di Laravel Exception Handler dan diterjemahkan menjadi pesan error yang bermakna bagi user (bukan pesan teknis mentah).

**Alasan Exception khusus per domain:** memungkinkan Handler membedakan "kesalahan pengguna yang wajar" (mis. mencoba check-in Habit dua kali) dari "kesalahan sistem yang tidak terduga" — keduanya butuh penanganan tampilan dan logging yang berbeda.

---

## 24. Logging Strategy

Log channel dipisah menjadi tiga stack sesuai Blueprint bagian 18: **application** (error umum), **jobs** (proses background — reminder, agregasi Review), dan **security** (percobaan login gagal, perubahan kepemilikan data). Setiap Event penting dari bagian 13 (TDD) juga menuliskan entri log ringkas di channel `jobs` untuk memudahkan penelusuran alur efek samping lintas modul saat debugging.

---

## 25. Error Reporting

Tahap awal: laporan error cukup lewat Log channel `application` (Blueprint bagian 18). Begitu aplikasi mulai dipakai harian secara stabil (mendekati v1.0), ditambahkan **Sentry** (Blueprint bagian 25) untuk menangkap exception production secara real-time dengan stack trace lengkap, tanpa perlu solo developer mengecek log manual setiap hari.

---

## 26. Security Strategy

Mengikuti Blueprint bagian 20 secara penuh, dengan penambahan detail teknis:
- HTTPS wajib di semua environment kecuali local.
- Password hashing via driver bawaan Laravel (`bcrypt`, konfigurasi default — tidak dikustomisasi).
- Kredensial seluruhnya di `.env`, tervalidasi tidak ter-commit lewat `.gitignore` bawaan Laravel.
- CSRF protection bawaan tidak dinonaktifkan di form mana pun.
- Mass assignment dicegah dengan `$fillable` eksplisit di setiap Model (bukan `$guarded = []`), agar setiap field yang bisa diisi massal terlihat jelas dan disengaja.

---

## 27. Rate Limiting

- Endpoint login: maksimal 5 percobaan per menit per kombinasi email+IP (bawaan Laravel Fortify), mencegah brute force meski aplikasi single-user.
- Endpoint capture Inbox (Quick Capture): rate limit longgar (mis. 60 request/menit) — cukup untuk mencegah penyalahgunaan/bug infinite-loop di sisi klien, tanpa mengganggu penggunaan wajar yang butuh capture cepat berulang kali.
- API (saat diaktifkan): rate limit per token Sanctum, mengikuti default Laravel (`throttle:api`).

---

## 28. Performance Optimization

Mengikuti Blueprint bagian 21, ditambah detail: setiap query Dashboard menggunakan `select()` eksplisit (tidak `select *`) dan `with()` eager loading untuk relasi Task→Project→Goal sekaligus, untuk meminimalkan jumlah query dan ukuran payload. Index database ditambahkan pada kombinasi kolom yang sering difilter bersamaan (mis. composite index `(user_id, status, due_date)` pada tabel `tasks`), bukan hanya index tunggal per kolom.

---

## 29. File Storage Strategy

Mengikuti Blueprint bagian 15: driver `local` di awal, dikonfigurasi lewat `config/filesystems.php` agar berpindah ke driver `s3` (kompatibel R2/Spaces) hanya dengan mengubah environment variable, tanpa mengubah kode pemanggil (`Storage::disk('attachments')->put(...)` tetap sama terlepas driver aktif). Path file mengikuti konvensi `{user_id}/{modul}/{tahun}/{bulan}/{filename}` sesuai Blueprint.

---

## 30. Backup Strategy

Mengikuti Blueprint bagian 23 tanpa perubahan: `spatie/laravel-backup` dijadwalkan harian via Laravel Scheduler, disimpan ke disk terpisah (object storage eksternal) dari server aplikasi, dengan retensi berlapis (harian/mingguan/bulanan) dan pengujian restore berkala sebagai bagian dari Maintenance Strategy (Blueprint bagian 16).

---

## 31. API Strategy (Internal & Future Public API)

**Internal saat ini:** tidak ada API eksternal aktif — seluruh interaksi lewat Blade+Livewire dalam satu request-response siklus session.

**Disiapkan untuk masa depan:** `routes/api.php` dan Laravel Sanctum sudah terpasang sejak awal (Blueprint bagian 14); API Resource class (`app/Http/Resources/`) dibuat berdampingan dengan setiap Model utama sejak MVP — bukan ditunda — agar transformasi output API tidak perlu ditulis ulang dari nol saat endpoint benar-benar diaktifkan. Endpoint publik (jika Blueprint bagian 18/19 terealisasi — mobile companion/multi-user) mengikuti konvensi RESTful resource standar Laravel.

**Alasan menyiapkan API Resource sejak awal meski API belum aktif:** menulis API Resource di awal (bahkan tanpa route aktif) jauh lebih murah dibanding menulis ulang transformasi data belakangan saat struktur Model sudah lebih kompleks dan lebih banyak dipakai di berbagai tempat.

---

## 32. Coding Convention

- Mengikuti **PSR-12** sebagai standar dasar, ditegakkan otomatis lewat **Laravel Pint** (bawaan ekosistem Laravel modern) — bukan konfigurasi linter custom yang perlu dirawat terpisah.
- Setiap Action class hanya memiliki satu tanggung jawab (Single Responsibility) dan idealnya satu method publik (`execute()`), sejalan bagian 7.
- Method Model dibatasi hanya untuk logika yang **benar-benar milik entitas itu sendiri** (mis. accessor/mutator sederhana); logika lintas-entitas selalu di Action, tidak di Model (menghindari "fat model").

---

## 33. Naming Convention

| Jenis | Konvensi | Contoh |
|---|---|---|
| Model | Singular, PascalCase | `Task`, `InboxItem` |
| Tabel | Plural, snake_case | `tasks`, `inbox_items` |
| Action | VerbNoun + `Action` | `CompleteTaskAction` |
| Event | Entity + past tense | `TaskCompleted` |
| Listener | Verb-phrase deskriptif | `RecalculateProjectProgress` |
| Enum | Entity + Attribute | `TaskStatus` |
| Policy | Entity + `Policy` | `TaskPolicy` |
| Form Request | Verb + Entity + `Request` | `StoreTaskRequest` |
| Livewire Component | Domain-prefixed | `Tasks\TaskList`, `Habits\HabitChecklist` |
| Route name | `{modul}.{aksi}` | `tasks.complete` |

**Alasan konvensi konsisten dan dapat ditebak:** solo developer yang kembali ke kode setelah jeda (mis. beberapa minggu tidak menyentuh satu modul) harus dapat menebak nama file/class tanpa perlu mencari-cari — konsistensi penamaan adalah bentuk dokumentasi implisit yang paling murah untuk dirawat.

---

## 34. Testing Strategy

Mengikuti Blueprint bagian 11 dengan detail teknis: **PestPHP** direkomendasikan sebagai testing framework (sintaks lebih ringkas dibanding PHPUnit murni, mempercepat penulisan test untuk solo developer) dengan dua lapis prioritas:

1. **Unit test** untuk seluruh Action yang berisi kalkulasi/aturan bisnis (`RecalculateProjectProgressAction`, `RecalculateHabitStreakAction`, transisi status di setiap Enum).
2. **Feature test** untuk alur inti end-to-end per Use Case FSD (capture → triage, create → complete Task, check-in Habit, dsb.), dijalankan terhadap route/Livewire component sungguhan dengan database test terpisah (SQLite in-memory untuk kecepatan, kecuali ada fitur spesifik PostgreSQL yang diuji seperti full-text search).

---

## 35. Git Workflow

Mengikuti Blueprint bagian 13: trunk-based sederhana, commit granular per perubahan logis, pesan commit mengikuti pola `{modul}: {deskripsi singkat}` (mis. `tasks: add streak recalculation on habit check-in`).

---

## 36. Branch Strategy

Mengikuti Blueprint bagian 14 tanpa perubahan: `main` (selalu deployable) + `feature/*` berumur pendek + `fix/*` untuk perbaikan bug — tidak ada branch `develop`/`release` terpisah, karena tidak ada tim lain yang perlu dikoordinasikan lewat percabangan kompleks.

---

## 37. CI/CD Recommendation

**CI:** GitHub Actions (atau setara) menjalankan otomatis pada setiap push ke `feature/*` dan setiap merge ke `main`: Laravel Pint (format check), test suite Pest, dan `composer audit` (memeriksa vulnerability dependency) — seluruhnya harus lolos sebelum merge diperbolehkan ke `main`.

**CD:** deployment ke production dipicu otomatis setelah merge ke `main` lolos CI, menggunakan mekanisme zero-downtime dari platform hosting terpilih (mis. Laravel Forge deploy script, atau setara) sesuai Blueprint bagian 24.

**Alasan CI wajib sejak awal meski solo developer:** CI menjadi "rekan kerja pengganti" yang menangkap regresi sebelum sampai production — krusial justru karena tidak ada reviewer manusia lain yang akan meninjau setiap perubahan kode.

---

## 38. Deployment Strategy

Mengikuti Blueprint bagian 24: managed VPS dengan panel deployment (mis. Laravel Forge) atau PaaS pendukung Laravel native, dua environment (`local`, `production`), migration dijalankan otomatis sebagai bagian dari langkah deploy (`php artisan migrate --force`), dan queue worker (Horizon) di-restart otomatis pasca-deploy agar Job yang sedang berjalan menggunakan kode terbaru.

---

## Penutup

Dokumen TDD ini menerjemahkan seluruh keputusan Blueprint v1.0 dan spesifikasi fungsional FSD menjadi keputusan implementasi teknis yang konkret: struktur folder, pola desain (Action, Event/Listener, Enum), serta strategi lintas-cutting concern (cache, queue, security, testing). Developer dapat mulai menulis migration dan class pertama (disarankan mulai dari domain `Shared` dan `Tasks`, mengikuti urutan dependency di Blueprint bagian 12.2) tanpa perlu mengambil keputusan arsitektur baru di tengah proses — setiap pertanyaan "bagaimana ini seharusnya distruktur" sudah terjawab di salah satu dari 38 bagian di atas.
