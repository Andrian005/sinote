# PERSONAL OS — IMPLEMENTATION GUIDE & DEVELOPMENT EXECUTION PLAN
### Panduan Harian Coding — Berdasarkan Blueprint v1.0, FSD, TDD, Database Spec & UI/UX Spec

---

## Catatan Konsistensi

Seluruh dokumen sebelumnya ditinjau ulang untuk menyusun panduan eksekusi ini. **Tidak ditemukan inkonsistensi baru** yang memerlukan perubahan keputusan arsitektur, database, atau desain — dokumen ini murni menyusun **urutan eksekusi** dari keputusan yang sudah final di lima dokumen sebelumnya. Satu hal yang perlu ditegaskan ulang (bukan perubahan, hanya penegasan urutan): modul `Shared` (User, Tag, NotificationPreference) **bukan modul fitur**, melainkan fondasi lintas modul — di dokumen ini ia diperlakukan sebagai bagian dari "Core Infrastructure", bukan dihitung sebagai satu modul tersendiri dalam Module Development Sequence, agar tidak menimbulkan kerancuan penomoran dengan 12 modul fitur di FSD.

---

## 1. Development Strategy

**Strategi terpilih: Iteratif-Vertikal dengan Fondasi-Dulu (Foundation-First Vertical Slicing).**

Berbeda dari pendekatan vertikal murni di Blueprint bagian 12.1 (yang menekankan "thin slice dari seluruh siklus secepat mungkin"), dokumen eksekusi ini menambahkan satu prasyarat eksplisit: **Core Infrastructure harus 100% selesai sebelum thin slice pertama dimulai**, karena tanpa fondasi Auth, `user_id` scoping, Enum, dan Tagging yang sudah bekerja, thin slice pertama akan berisiko dibangun di atas asumsi yang keliru dan perlu dirombak ulang.

**Alasan:** TDD bagian 5 & 20 menegaskan `user_id` scoping dan Policy dibangun *sejak awal*, bukan ditambahkan belakangan — ini secara implisit berarti fondasi tersebut harus benar-benar berjalan (bukan sekadar direncanakan) sebelum modul fitur pertama (Inbox) disentuh. Setelah fondasi berdiri, sisa pengembangan mengikuti pendekatan vertikal Blueprint apa adanya: setiap slice menghasilkan sesuatu yang bisa langsung didogfooding.

---

## 2. Development Order

```
1. Environment Setup
2. Core Infrastructure       (Auth, User, Enum dasar, Policy dasar, base Layer)
3. Shared Components         (Tagging/Context — FSD Modul 4)
4. Core Module                (Inbox → Tasks → Projects & Goals — siklus inti)
5. Supporting Module          (Dashboard, Deadline Reminder)
6. Advanced Feature (Tier 1)  (Habit Tracking, Knowledge Base, Focus Mode)
7. Advanced Feature (Tier 2)  (Review & Reflection, Full Notification Engine, Search)
8. Optimization                (Cache, index, performance hardening)
9. Deployment                  (Hardening keamanan, CI/CD, rilis v1.0)
```

**Alasan dependency per tahap:**
- **Environment → Core Infrastructure:** tidak ada baris kode fitur yang bisa ditulis tanpa Auth berjalan (seluruh Policy bergantung pada `$user` yang login — TDD bagian 19–20).
- **Core Infrastructure → Shared Components (Tagging):** Tagging dinaikkan menjadi Must Have di Blueprint (hasil audit) justru karena Task/Project/Note/Habit di tahap berikutnya langsung membutuhkan relasi polymorphic ini sejak baris kode pertama ditulis — membangunnya belakangan berarti migration ulang pada tabel yang sudah berisi data.
- **Shared → Core Module:** Inbox→Tasks→Projects&Goals adalah rantai dependency data langsung (Inbox melahirkan Task, Task dinaungi Project) — urutan ini **tidak bisa dibalik** (Database Spec Bagian G, Migration Order).
- **Core Module → Supporting Module:** Dashboard (FSD Modul 5) murni agregasi read-only dari Task/Habit — tidak ada gunanya dibangun sebelum ada data Task untuk diagregasi. Deadline Reminder butuh Task/Project dengan `due_date` untuk dijadwalkan.
- **Supporting → Advanced Tier 1:** Habit, Knowledge Base, dan Focus Mode masing-masing relatif independen satu sama lain (lihat Dependency Map bagian 6) sehingga dikelompokkan sebagai satu tier yang **boleh dikerjakan dalam urutan fleksibel** di dalamnya.
- **Advanced Tier 1 → Tier 2:** Review butuh data historis Task/Project/Habit yang sudah berjalan; Full Notification Engine butuh Habit (jadwal) dan Review (ritual) sudah ada; Search butuh seluruh sumber data (Task/Project/Note/Habit) sudah terbentuk untuk diindeks — ketiganya secara struktural **harus** menunggu tier sebelumnya (konsisten Blueprint bagian 12.2, hasil audit #1).
- **→ Optimization → Deployment:** optimasi performa/cache baru bermakna setelah seluruh query nyata (bukan asumsi) sudah ada untuk diukur; deployment penuh (CI/CD, hardening) ditutup di akhir agar tidak mengganggu ritme development dengan proses rilis yang belum perlu di tahap awal.

---

## 3. Module Development Sequence

*(Estimasi waktu mengasumsikan ritme solo developer paruh waktu ± 8–10 jam efektif/minggu, sesuai Blueprint bagian 7)*

### 3.0 Core Infrastructure *(bukan modul fitur — fondasi wajib)*
- **Tujuan:** Auth berjalan, `users` migration, base Enum/Policy pattern siap dipakai ulang.
- **Dependency:** Tidak ada (titik awal).
- **Prioritas:** Wajib, tidak dapat ditunda.
- **Kompleksitas:** Rendah–Sedang.
- **Estimasi Waktu:** 3–5 hari.
- **Risiko:** Tergoda melompat ke fitur sebelum Policy dasar benar-benar teruji — mitigasi: checklist "setelah selesai" wajib dicentang penuh sebelum lanjut.
- **Checklist sebelum mulai:** Laravel + PostgreSQL + Redis lokal aktif (Blueprint bagian 24); repository Git dengan branch strategy siap (Blueprint bagian 14).
- **Checklist setelah selesai:** Login/logout berfungsi; satu Policy contoh (`UserPolicy` atau dummy) teruji manual; struktur folder `app/Domain/*` sesuai TDD bagian 3 sudah dibuat kosong sebagai kerangka.

### 3.1 Shared: Tagging/Context
- **Tujuan:** Lapisan metadata polymorphic (FSD Modul 4).
- **Dependency:** Core Infrastructure.
- **Prioritas:** Must Have (fondasi, hasil audit Blueprint #4).
- **Kompleksitas:** Sedang (relasi polymorphic butuh perhatian ekstra — TDD bagian 9).
- **Estimasi Waktu:** 2–3 hari.
- **Risiko:** Menunda modul ini dengan alasan "nanti saja" adalah risiko tertinggi di seluruh roadmap — jika ditunda, migration Task/Project/Note/Habit di tahap berikutnya harus diubah ulang untuk menambah relasi `taggables`.
- **Checklist sebelum mulai:** Core Infrastructure selesai penuh.
- **Checklist setelah selesai:** Tag dapat dibuat, dilekatkan, dan dilepas dari entitas dummy di unit test — tanpa perlu UI penuh dulu.

### 3.2 Core Module: Inbox
- **Tujuan:** Capture & triase (FSD Modul 1).
- **Dependency:** Core Infrastructure.
- **Prioritas:** Must Have — MVP 0 (Blueprint bagian 12.2, v0.1).
- **Kompleksitas:** Rendah.
- **Estimasi Waktu:** 2–3 hari.
- **Risiko:** Rendah — modul paling sederhana dalam roadmap, cocok sebagai "kemenangan cepat" pertama.
- **Checklist sebelum mulai:** Core Infrastructure selesai.
- **Checklist setelah selesai:** Quick Capture dapat dipakai sehari-hari (dogfooding dimulai dari titik ini); triase ke Task berfungsi (meski Task masih model kosong minimal).

### 3.3 Core Module: Tasks
- **Tujuan:** Unit eksekusi harian (FSD Modul 2).
- **Dependency:** Core Infrastructure, Tagging/Context, Inbox (sebagai sumber konversi).
- **Prioritas:** Must Have — MVP 0/1.
- **Kompleksitas:** Sedang.
- **Estimasi Waktu:** 4–6 hari.
- **Risiko:** State Machine Task (todo/in_progress/done/archived) salah diimplementasikan berdampak ke seluruh modul turunan (Project progress, Dashboard, Reminder) — mitigasi: unit test State Machine wajib selesai sebelum lanjut ke Project & Goals.
- **Checklist sebelum mulai:** 3.1 dan 3.2 selesai penuh.
- **Checklist setelah selesai:** CRUD Task lengkap; transisi status sesuai Database Spec Bagian B.1 teruji unit test; Task dapat berdiri bebas tanpa Project (relasi opsional teruji).

### 3.4 Core Module: Projects & Goals
- **Tujuan:** Struktur menengah–panjang (FSD Modul 3).
- **Dependency:** Tasks.
- **Prioritas:** Must Have — MVP 1 (v0.2).
- **Kompleksitas:** Sedang–Tinggi (perhitungan progres agregat).
- **Estimasi Waktu:** 5–7 hari.
- **Risiko:** Immutable `type` pada Goal (Database Spec B.3) mudah terlewat jika tidak divalidasi eksplisit di Action — mitigasi: test khusus mencoba mengubah `type` dan memastikan ditolak.
- **Checklist sebelum mulai:** 3.3 selesai penuh, termasuk unit test `TaskCompleted` Event.
- **Checklist setelah selesai:** Progres Project terhitung otomatis dan akurat terhadap perubahan status Task (unit test `RecalculateProjectProgressAction`); Goal berujung vs berkelanjutan berperilaku berbeda sesuai Business Rules.

**→ Titik ini menandai selesainya Milestone 1 ("Bisa Dipakai") — lihat bagian 7.**

### 3.5 Supporting: Dashboard
- **Tujuan:** Agregasi "apa yang penting sekarang" (FSD Modul 5).
- **Dependency:** Tasks, Projects & Goals.
- **Prioritas:** Must Have — MVP 1.
- **Kompleksitas:** Sedang (query agregasi + akan di-cache belakangan).
- **Estimasi Waktu:** 3–4 hari.
- **Risiko:** Query N+1 jika eager loading tidak diterapkan sejak awal (TDD bagian 28) — mitigasi: review query log manual saat pertama kali dibangun.
- **Checklist sebelum mulai:** 3.4 selesai.
- **Checklist setelah selesai:** Dashboard menampilkan subset Task prioritas + placeholder kosong untuk Habit (belum ada modul Habit) tanpa error.

### 3.6 Supporting: Deadline Reminder
- **Tujuan:** Reminder berbasis deadline (FSD Modul 6).
- **Dependency:** Tasks, Projects & Goals.
- **Prioritas:** Must Have — MVP 1.
- **Kompleksitas:** Sedang–Tinggi (scheduled job + queue).
- **Estimasi Waktu:** 4–5 hari.
- **Risiko:** Tabel `reminders` dan `notification_preferences` harus sudah ada sesuai Database Spec A.11–A.12 sebelum modul ini disentuh — mitigasi: migration keduanya dibuat di tahap ini meski `notification_preferences` baru dipakai penuh nanti oleh Full Notification Engine.
- **Checklist sebelum mulai:** 3.5 selesai (agar reminder aktif dapat langsung ditampilkan di Dashboard).
- **Checklist setelah selesai:** Reminder H-1/H-hari terkirim dan tampil di Dashboard; reminder otomatis batal saat Task diselesaikan lebih awal.

**→ Titik ini menandai selesainya MVP 1 penuh (v0.2) — dogfooding harian penuh dimulai dari sini.**

### 3.7 Advanced Tier 1: Habit Tracking
- **Tujuan:** Pelacakan kebiasaan (FSD Modul 7).
- **Dependency:** Core Infrastructure, Tagging/Context (tidak bergantung pada Tasks/Projects).
- **Prioritas:** Should Have — v0.3.
- **Kompleksitas:** Sedang (logika streak).
- **Estimasi Waktu:** 4–6 hari.
- **Risiko:** Perubahan frekuensi Habit yang salah menghitung ulang streak lama secara retroaktif (dilarang di Business Rules B.4) — mitigasi: test eksplisit mengubah frekuensi di tengah riwayat `HabitLog`.
- **Checklist sebelum mulai:** Core Infrastructure & Tagging selesai (independen dari Tasks/Projects).
- **Checklist setelah selesai:** Habit muncul di Dashboard (mengisi placeholder dari 3.5); check-in dan streak akurat teruji unit test.

### 3.8 Advanced Tier 1: Knowledge Base
- **Tujuan:** Arsip catatan/referensi (FSD Modul 8).
- **Dependency:** Core Infrastructure, Tagging/Context, Projects (untuk linking opsional).
- **Prioritas:** Should Have — v0.4.
- **Kompleksitas:** Rendah–Sedang.
- **Estimasi Waktu:** 3–5 hari.
- **Risiko:** Relasi `project_id` nullable dengan `set null on delete` (Database Spec A.8) harus diuji eksplisit — mitigasi: test menghapus Project dan memastikan Note tidak ikut hilang.
- **Checklist sebelum mulai:** 3.4 selesai (untuk linking); dapat dikerjakan paralel dengan 3.7 (lihat Dependency Map).
- **Checklist setelah selesai:** Note dapat dibuat, diedit (auto-save sesuai UI/UX Spec A.6), ditautkan/dilepas dari Project.

### 3.9 Advanced Tier 1: Focus Mode
- **Tujuan:** Eksekusi bebas distraksi (FSD Modul 9).
- **Dependency:** Tasks (satu-satunya sumber data — tidak ada entitas sendiri).
- **Prioritas:** Should Have — v0.4.
- **Kompleksitas:** Rendah (murni state UI di atas Task yang sudah ada — TDD & Blueprint klarifikasi).
- **Estimasi Waktu:** 2–3 hari.
- **Risiko:** Risiko paling rendah di seluruh roadmap — modul ini tidak menyentuh database sama sekali.
- **Checklist sebelum mulai:** 3.3 selesai.
- **Checklist setelah selesai:** Focus session dapat dimulai dari Task manapun (Dashboard/Project/All-Tasks) dan menandai selesai tanpa keluar dari mode.

**→ Titik ini menandai selesainya Milestone 2 ("Habit & Fokus") — lihat bagian 7.**

### 3.10 Advanced Tier 2: Review & Reflection
- **Tujuan:** Ritual refleksi berkala (FSD Modul 10).
- **Dependency:** Tasks, Projects & Goals, Habit Tracking (sebagai sumber agregasi).
- **Prioritas:** Should Have — v0.5.
- **Kompleksitas:** Sedang–Tinggi (agregasi historis + snapshot beku).
- **Estimasi Waktu:** 4–6 hari.
- **Risiko:** `snapshot_metrics` yang **tidak sengaja** dihitung ulang otomatis (harus dibekukan permanen — Business Rules B.8) — mitigasi: test eksplisit memastikan nilai tidak berubah meski data sumber berubah setelah Review dibuat.
- **Checklist sebelum mulai:** 3.7 selesai (Habit harus punya data historis untuk diagregasi).
- **Checklist setelah selesai:** Daily/Weekly/Monthly Review menghasilkan snapshot akurat dan beku; catatan refleksi tersimpan (auto-save).

### 3.11 Advanced Tier 2: Full Notification Engine
- **Tujuan:** Reminder jadwal Habit + ritual Review (FSD Modul 11).
- **Dependency:** Deadline Reminder (lapisan dasar sudah ada), Habit Tracking, Review & Reflection.
- **Prioritas:** Could Have — v0.6.
- **Kompleksitas:** Tinggi (scheduling kondisional lintas modul).
- **Estimasi Waktu:** 5–7 hari.
- **Risiko:** Duplikasi reminder jika tidak mengecek ulang kondisi tepat sebelum kirim (FSD 11.1) — mitigasi: test skenario Habit dicentang tepat sebelum job berjalan.
- **Checklist sebelum mulai:** 3.6, 3.7, dan 3.10 selesai penuh.
- **Checklist setelah selesai:** Reminder Habit dan ritual Review terkirim sesuai jadwal preferensi user; tidak ada reminder ganda dalam satu hari untuk entitas yang sama.

### 3.12 Advanced Tier 2: Search
- **Tujuan:** Pencarian lintas modul (FSD Modul 12).
- **Dependency:** Seluruh modul data (Task, Project, Note, Habit) sebagai sumber index.
- **Prioritas:** Could Have — v0.6.
- **Kompleksitas:** Sedang.
- **Estimasi Waktu:** 3–4 hari.
- **Risiko:** Rendah — modul read-only, kegagalan tidak merusak data apa pun.
- **Checklist sebelum mulai:** 3.3, 3.4, 3.7, 3.8 selesai (seluruh sumber data ada).
- **Checklist setelah selesai:** Query mengembalikan hasil terkategorikan dari seluruh entitas, difilter kepemilikan user.

**→ Titik ini menandai selesainya Milestone 4 (menuju v1.0) bersama tahap Optimization & Deployment di bawah.**

---

## 4. Laravel Implementation Structure

Struktur file berikut berlaku **identik untuk setiap modul** (kecuali disebutkan berbeda) — mengikuti TDD bagian 3, 5–11:

| File | Fungsi |
|---|---|
| **Migration** | Mendefinisikan skema tabel persis sesuai Database Spec Bagian A. |
| **Model (Eloquent)** | Representasi data + relasi + accessor ringan; **tidak** berisi logika bisnis lintas-entitas (TDD bagian 32). |
| **Enum** | Status bertingkat entitas (backed enum — TDD bagian 10), termasuk method transisi valid. |
| **Factory** | Data uji dengan state method eksplisit per status (TDD/Database Spec Bagian I). |
| **Seeder** | Hanya untuk data wajib produksi (mis. `NotificationPreferenceSeeder`) — bukan data uji. |
| **Policy** | Aturan otorisasi kepemilikan per entitas (TDD bagian 20). |
| **Form Request** | Validasi input per aksi (Create/Update terpisah — TDD bagian 22). |
| **Action** | Satu class = satu operasi bisnis (TDD bagian 7). |
| **Event** | Menandai fakta bisnis penting yang terjadi (mis. `TaskCompleted`). |
| **Listener** | Efek samping lintas modul yang bereaksi terhadap Event (TDD bagian 13). |
| **Notification** | Pesan yang dikirim ke user lewat channel `database` (TDD bagian 14). |
| **Job** | Proses asinkron di queue (scanner reminder, agregasi Review — TDD bagian 15). |
| **Livewire Component** | Menerima input, memanggil Action, tidak berisi logika bisnis (TDD bagian 4). |
| **Blade View** | Tampilan, mengikuti komponen dan token dari UI/UX Spec Bagian B. |
| **Route** | Terdaftar di `routes/web.php`, dikelompokkan per modul (TDD bagian 4). |
| **Test (Pest)** | Unit test untuk Action/Enum berisiko tinggi, Feature test untuk alur end-to-end (TDD bagian 34). |

**Catatan khusus per modul:**
- **Focus Mode:** tidak memiliki Migration/Model/Factory/Seeder sendiri (murni Livewire Component + Blade View di atas Model Task yang sudah ada).
- **Dashboard & Search:** tidak memiliki Migration/Model sendiri (read-only aggregation layer) — hanya Livewire Component, Blade View, dan Action agregasi (mis. `GetDashboardDataAction`, `SearchAcrossModulesAction`).
- **Tagging/Context:** Model `Tag` + tabel pivot `taggables` tanpa Model Eloquent terpisah untuk pivot (cukup relasi `morphToMany` di setiap Model taggable).

---

## 5. Coding Order (per Modul)

Urutan berikut berlaku di dalam **setiap** modul fitur (3.2–3.12), berdasarkan alasan teknis berikut:

1. **Migration** — skema harus ada sebelum kode apa pun dapat berjalan.
2. **Enum** — didefinisikan sebelum Model, karena Model akan mengetik-cast kolom status ke Enum ini sejak awal.
3. **Model + Relationship** — struktur data dan relasinya (termasuk `morphToMany` Tag jika relevan).
4. **Factory** — dibuat segera setelah Model, agar seluruh langkah berikutnya (termasuk menulis Action) dapat langsung diuji dengan data sintetis, bukan menunggu UI selesai.
5. **Policy** — otorisasi harus ada sebelum Action/Controller dapat dipanggil dengan aman, meski aturannya sederhana di tahap ini.
6. **Form Request (Validation)** — aturan input sesuai FSD per fitur.
7. **Action** — logika bisnis inti, diuji lewat **unit test** langsung (memakai Factory dari langkah 4) sebelum menyentuh HTTP layer sama sekali.
8. **Event & Listener** (jika modul memicu efek lintas modul sesuai Dependency Map bagian 13 TDD) — ditulis setelah Action inti teruji, karena Listener bereaksi terhadap Event yang di-dispatch Action.
9. **Notification/Job** (jika modul memiliki komponen reminder/asinkron) — dibangun setelah Action/Event dasar stabil.
10. **Livewire Component + Blade View (UI)** — dibangun paling akhir dari sisi logika, karena UI hanya memanggil Action yang sudah teruji benar terlebih dahulu.
11. **Feature Test** — menguji alur end-to-end lewat UI/route sungguhan, menutup siklus modul tersebut.
12. **Seeder** (jika relevan untuk data wajib produksi) — ditambahkan terakhir setelah seluruh alur teruji.

**Alasan urutan "logika dulu, UI belakangan":** ini adalah penerapan langsung dari TDD bagian 6–7 (Service Layer/Action Pattern) — memisahkan "apa yang terjadi" dari "bagaimana dipicu" berarti Action harus benar dan teruji **sebelum** dihubungkan ke Livewire, bukan ditulis bersamaan. Menulis UI lebih dulu (pola umum yang tergoda dilakukan karena "terasa lebih terlihat progresnya") berisiko menyembunyikan bug logika bisnis di balik tampilan yang tampak berfungsi.

---

## 6. Dependency Map

```
                         Core Infrastructure (Auth, User)
                                    │
                          Tagging/Context (Shared)
                                    │
                ┌───────────────────┼───────────────────┐
                │                   │                   │
             Inbox                Habit               Knowledge Base
                │                (independen)         (butuh Projects utk link,
                ▼                                       dapat mulai paralel
              Tasks                                     dgn struktur dasarnya)
                │
                ▼
        Projects & Goals
                │
      ┌─────────┼─────────┐
      ▼         ▼         ▼
  Dashboard  Deadline    Focus Mode
            Reminder    (hanya butuh Tasks)
      │         │
      └────┬────┘
           ▼
   Review & Reflection ◄──── Habit (data historis)
           │
           ▼
  Full Notification Engine ◄──── Deadline Reminder + Habit
           │
           ▼
        Search ◄──── Tasks, Projects, Notes, Habit (seluruh sumber data)
```

**Modul yang harus selesai lebih dulu (urutan ketat, tidak dapat dibalik):** Core Infrastructure → Tagging → Inbox → Tasks → Projects & Goals → (Dashboard & Deadline Reminder) → Review → Full Notification Engine → Search.

**Modul yang dapat dikerjakan paralel:**
- **Habit Tracking** dapat dikerjakan kapan saja setelah Tagging selesai — **tidak bergantung** pada Tasks/Projects sama sekali, sehingga dapat disisipkan lebih awal jika solo developer ingin variasi pekerjaan (mis. dikerjakan bersamaan dengan finishing touch Projects & Goals).
- **Knowledge Base** dan **Focus Mode** dapat dikerjakan dalam urutan bebas satu sama lain setelah prasyarat masing-masing (Projects untuk Knowledge Base linking; Tasks untuk Focus Mode) terpenuhi.

**Modul yang bergantung pada modul lain secara ketat:** Dashboard (Tasks+Habit), Deadline Reminder (Tasks+Projects), Review (Tasks+Projects+Habit), Full Notification Engine (Deadline Reminder+Habit+Review), Search (seluruh sumber data).

---

## 7. Milestone

### Milestone 1 — "Bisa Dipakai" (v0.1–v0.2)
- **Target:** Siklus Capture→Organize→Prioritize berjalan penuh.
- **Deliverable:** Core Infrastructure, Tagging, Inbox, Tasks, Projects & Goals, Dashboard, Deadline Reminder.
- **Kriteria Selesai:** Seluruh checklist "setelah selesai" pada 3.0–3.6 tercentang; dogfooding harian dimulai.
- **Risiko:** Tergoda menambah fitur Habit/Note sebelum siklus inti benar-benar stabil (scope creep — Blueprint bagian 13).
- **Checklist:** [ ] Auth berjalan [ ] Tagging teruji [ ] Inbox→Task konversi lancar [ ] Progres Project akurat [ ] Dashboard menampilkan data nyata [ ] Reminder deadline terkirim.

### Milestone 2 — "Habit & Fokus" (v0.3–v0.4)
- **Target:** Melampaui sekadar to-do list — kebiasaan dan sesi kerja terfokus.
- **Deliverable:** Habit Tracking, Knowledge Base, Focus Mode.
- **Kriteria Selesai:** Checklist 3.7–3.9 tercentang penuh.
- **Risiko:** Perhitungan streak salah tanpa disadari karena tidak diuji dengan skenario retroaktif.
- **Checklist:** [ ] Streak akurat [ ] Note ter-link/ter-unlink dari Project dengan benar [ ] Focus Mode dapat diakses dari 3 titik (Dashboard/Project/All-Tasks).

### Milestone 3 — "Reflektif" (v0.5)
- **Target:** Siklus penuh tertutup dengan ritual refleksi.
- **Deliverable:** Review & Reflection (Daily/Weekly/Monthly).
- **Kriteria Selesai:** Checklist 3.10 tercentang; minimal satu siklus mingguan penuh sudah didogfooding.
- **Risiko:** Snapshot metrik tidak benar-benar beku (bug paling kritis di modul ini).
- **Checklist:** [ ] Snapshot tidak berubah setelah data sumber berubah [ ] Reflection note auto-save berfungsi.

### Milestone 4 — "Matang & Stabil" (v0.6–v1.0)
- **Target:** Notifikasi menyeluruh, pencarian, dan hardening produksi.
- **Deliverable:** Full Notification Engine, Search, Optimization, Deployment.
- **Kriteria Selesai:** Checklist 3.11–3.12 tercentang, ditambah seluruh item Development Checklist bagian 9 (Setelah Coding) dan Deployment Guide bagian 13.
- **Risiko:** Menunda hardening keamanan/backup karena "aplikasi sudah terasa selesai secara fitur" — mitigasi: v1.0 **tidak** dianggap rilis sah tanpa Security Strategy (TDD bagian 26) dan Backup Strategy (TDD bagian 30) aktif.
- **Checklist:** [ ] Reminder Habit/Review tidak duplikat [ ] Search mengembalikan hasil akurat lintas modul [ ] Backup otomatis + uji restore berhasil [ ] CI/CD aktif.

---

## 8. Sprint Planning

*(Sprint mingguan, 3–5 task konkret per sprint — mengikuti Blueprint bagian 5, disusun ulang selaras Coding Order bagian 5 di atas)*

| Sprint | Fokus | Task Utama | Output | Risiko |
|---|---|---|---|---|
| 1 | Core Infrastructure | Setup env, migration `users`, Auth (Breeze/Fortify), Policy dasar | Login berfungsi | Env misconfigured (Redis/PostgreSQL belum aktif) |
| 2 | Tagging/Context | Migration `tags`+`taggables`, Model, unit test relasi polymorphic | Tag dapat dilekatkan ke entitas dummy | Unique constraint `(user_id,name)` tidak diuji |
| 3 | Inbox | Migration, Model, Enum, Action Capture, Livewire capture form | Quick Capture dipakai harian | Validasi panjang teks terlewat |
| 4 | Inbox Triage + awal Tasks | Action Triage, migration `tasks`, Enum `TaskStatus` | Inbox→Task konversi jalan | State Machine Task salah transisi |
| 5 | Tasks lanjutan | CRUD Task penuh, `CompleteTaskAction`, Event `TaskCompleted`, unit test | Task dapat dikelola penuh | Reopen logic terlewat |
| 6 | Projects & Goals (bag. 1) | Migration `goals`+`projects`, Model, Action Create | Struktur dasar ada | `type` Goal tidak immutable |
| 7 | Projects & Goals (bag. 2) | `RecalculateProjectProgressAction`, Listener `TaskCompleted`, unit test | Progres akurat otomatis | Progres salah hitung Task `archived` |
| 8 | Dashboard | Action agregasi, Livewire component, cache dasar | Landing page berfungsi | N+1 query |
| 9 | Deadline Reminder | Migration `reminders`+`notification_preferences`, Job scanner, Notification | Reminder terkirim H-1/H | Job tidak ter-schedule dengan benar |
| — | **Milestone 1 selesai — mulai dogfooding penuh** | | | |
| 10 | Habit (bag. 1) | Migration `habits`+`habit_logs`, Model, Enum, Action Define | Habit dapat didefinisikan | Frequency validation |
| 11 | Habit (bag. 2) | `CheckInHabitAction`, `RecalculateHabitStreakAction`, unit test retroaktif | Streak akurat | Streak salah saat frekuensi berubah |
| 12 | Knowledge Base | Migration `notes`, Model, Action, auto-save Livewire | Note dapat dibuat/ditaut | `project_id` set-null tidak teruji |
| 13 | Focus Mode | Livewire full-screen component di atas Task | Sesi fokus berfungsi | State timer hilang saat refresh (diterima sebagai batasan MVP) |
| — | **Milestone 2 selesai** | | | |
| 14 | Review (bag. 1) | Migration `review_entries`, Model, Action agregasi Daily | Daily Review berfungsi | Snapshot tidak benar-benar beku |
| 15 | Review (bag. 2) | Action agregasi Weekly/Monthly, unit test snapshot freeze | Weekly/Monthly Review berfungsi | Query agregasi lambat tanpa index |
| — | **Milestone 3 selesai** | | | |
| 16 | Full Notification Engine (bag. 1) | Job scanner Habit schedule, Notification | Reminder Habit terkirim | Duplikasi reminder |
| 17 | Full Notification Engine (bag. 2) | Job scanner Review ritual, integrasi preference | Reminder ritual terkirim | Waktu kirim tidak sesuai preferensi user |
| 18 | Search | Action pencarian lintas Model, Livewire search bar | Pencarian global berfungsi | Hasil tidak terfilter kepemilikan user |
| 19 | Optimization | Review index database, cache invalidation, query audit | Performa Dashboard/Search membaik | Index terlewat pada kolom yang sering difilter |
| 20 | Deployment Hardening | CI/CD, Sentry, backup terjadwal, security review | v1.0 siap rilis | Backup belum pernah diuji restore |

---

## 9. Development Checklist

### Sebelum Coding
- [ ] Environment lokal (Laravel, PostgreSQL, Redis) aktif dan diverifikasi.
- [ ] Repository Git dengan branch strategy `main`+`feature/*` siap (TDD bagian 36).
- [ ] Struktur folder `app/Domain/*` dasar sudah dibuat (TDD bagian 3).
- [ ] Seluruh 6 dokumen referensi (Blueprint, FSD, TDD, Database Spec, UI/UX Spec, dokumen ini) telah dibaca ulang sebelum modul terkait dimulai.

### Saat Coding
- [ ] Mengikuti Coding Order bagian 5 (logika sebelum UI) untuk setiap modul.
- [ ] Laravel Pint dijalankan sebelum setiap commit (TDD bagian 32).
- [ ] Unit test ditulis untuk setiap Action berisiko tinggi **sebelum** menyambungkannya ke Livewire (bukan setelahnya).
- [ ] Nama file/class mengikuti Naming Convention TDD bagian 33 tanpa pengecualian.

### Setelah Coding
- [ ] Refactoring ringan dilakukan jika satu Action/Model mulai menyerap tanggung jawab modul lain (lihat bagian 12).
- [ ] Bug yang ditemukan saat dogfooding dicatat dan diperbaiki sebelum memulai modul berikutnya (bukan ditumpuk).
- [ ] Query log ditinjau manual untuk mendeteksi N+1 sebelum modul dianggap selesai.
- [ ] Security review dasar (mass assignment `$fillable`, otorisasi Policy) dicek ulang per modul, bukan hanya di akhir.

---

## 10. Testing Guide

| Modul | Unit Test (Prioritas) | Feature Test (Prioritas) | Integration Test | Manual Testing | Edge Case Testing |
|---|---|---|---|---|---|
| Inbox | Rendah (logika sederhana) | **Tinggi** — alur capture→triage end-to-end | — | Dogfooding harian sejak awal | Teks kosong, teks sangat panjang |
| Tasks | **Tinggi** — transisi State Machine | **Tinggi** — create→complete→reopen | Dengan Project (relasi opsional) | Cek strikethrough animasi | Task tanpa Project, due_date masa lalu |
| Projects & Goals | **Tinggi** — `RecalculateProjectProgressAction` | Sedang — CRUD dasar | Dengan Tasks (Event `TaskCompleted`) | Cek progress bar visual | Goal tanpa Project, Project tanpa Task |
| Dashboard | Sedang — query agregasi | Sedang | Dengan Tasks+Habit | Cek performa loading | Tidak ada data hari ini |
| Deadline Reminder | **Tinggi** — logika scheduling/cancel | Sedang | Dengan Queue (job dispatch) | Cek waktu kirim aktual | Deadline diubah setelah reminder terjadwal |
| Habit | **Tinggi** — `RecalculateHabitStreakAction` | Sedang | Dengan `habit_logs` | Cek grid visual | Frekuensi berubah di tengah riwayat |
| Knowledge Base | Rendah | Sedang | Dengan Project (set-null) | Cek auto-save | Project dihapus saat Note tertaut |
| Focus Mode | Rendah (murni state UI) | Sedang | Dengan Tasks | Cek focus-trap keyboard | Refresh browser saat sesi aktif |
| Review | **Tinggi** — snapshot freeze | Sedang | Dengan Tasks+Projects+Habit | Cek isi snapshot vs data live | Periode tanpa aktivitas sama sekali |
| Full Notification Engine | **Tinggi** — kondisi kirim/skip | Sedang | Dengan Queue+Preference | Cek tidak ada duplikasi | Preference diubah di tengah hari |
| Search | Sedang | Sedang | Lintas seluruh Model | Cek relevansi hasil | Query 1 karakter, tidak ada hasil |

**Prioritas keseluruhan:** Unit test pada Action yang menghitung/transisi status (Tasks, Projects&Goals, Habit, Review, Notification Engine) **selalu diprioritaskan tertinggi** — modul read-only/UI-murni (Focus Mode, Dashboard tampilan, Knowledge Base) cukup diuji lewat Feature test dan dogfooding manual, sesuai Blueprint bagian 11 (testing difokuskan pada risiko tertinggi, bukan cakupan 100%).

---

## 11. Code Review Checklist

*(Diterapkan solo developer terhadap kode sendiri sebelum merge `feature/*` ke `main` — menggantikan peran reviewer manusia)*

- [ ] **Naming Convention** — sesuai TDD bagian 33 tanpa penyimpangan.
- [ ] **Clean Code** — satu Action = satu tanggung jawab (TDD bagian 7); tidak ada method >30–40 baris tanpa alasan kuat.
- [ ] **SOLID** — khususnya Single Responsibility (Action) dan Dependency Inversion (Action menerima dependency lewat constructor/parameter, bukan `new` langsung di dalam method).
- [ ] **Performance** — eager loading diterapkan; tidak ada query di dalam loop (N+1).
- [ ] **Security** — `$fillable` eksplisit; Policy dipanggil sebelum Action dieksekusi; tidak ada input mentah masuk ke query tanpa validasi.
- [ ] **Validation** — seluruh input lewat Form Request, sesuai FSD Validation Rules per fitur.
- [ ] **Error Handling** — Exception domain spesifik dipakai untuk kondisi bisnis yang sudah teridentifikasi di FSD (TDD bagian 23).
- [ ] **Logging** — Event penting lintas modul tercatat di channel `jobs` (TDD bagian 24).
- [ ] **Documentation** — nama Action/Event cukup deskriptif sehingga tidak memerlukan komentar tambahan untuk dipahami (self-documenting code sebagai prioritas di atas komentar).

---

## 12. Refactoring Strategy

**Kapan refactoring dilakukan:** setelah setiap modul selesai (checklist bagian 3 tercentang penuh), bukan di tengah penulisan satu Action — menyelesaikan satu unit kerja utuh terlebih dahulu, baru meninjau kualitasnya, mencegah refactoring prematur yang justru memperlambat momentum (Blueprint bagian 18/19: risiko kehilangan momentum).

**Menghindari technical debt:** mengikuti Blueprint bagian 10 — utang teknis diperbolehkan sadar hanya pada bagian non-fondasi (styling, fitur pencarian sederhana), **tidak** pada struktur `user_id`/Policy/desain relasi. Setiap utang yang diambil dicatat eksplisit (mis. `// TODO(debt): ...` dengan deskripsi alasan) dan ditinjau saat sesi maintenance bulanan (bagian 14).

**Menjaga kualitas kode:** Code Review Checklist (bagian 11) dijalankan sendiri secara disiplin di setiap merge ke `main`; Laravel Pint mencegah drift gaya kode; test suite yang tumbuh seiring modul mencegah regresi diam-diam saat refactoring dilakukan.

---

## 13. Deployment Guide

**Urutan environment:** `local` (development harian) → `production` langsung (Blueprint bagian 24: staging ditunda untuk solo developer, perubahan diuji memadai secara lokal + test suite).

**Sebelum deploy pertama ke production:**
- [ ] **Environment Variable**: seluruh `.env` production (DB, Redis, storage, mail) terisi benar, tidak ada default development yang tertinggal.
- [ ] **Migration**: `php artisan migrate --force` dijalankan sebagai bagian dari langkah deploy otomatis (TDD bagian 38).
- [ ] **Seeder**: hanya seeder produksi (`NotificationPreferenceSeeder` via Observer, bukan seeder data uji) yang dijalankan di production.
- [ ] **Queue**: Horizon aktif dan disupervisi proses (mis. Supervisor/systemd) agar restart otomatis jika worker berhenti.
- [ ] **Cache**: Redis cache di-clear pasca-deploy jika ada perubahan struktur data yang memengaruhi key cache lama.
- [ ] **Scheduler**: cron `* * * * * php artisan schedule:run` aktif di server (prasyarat mutlak agar seluruh Job reminder/Review berjalan).
- [ ] **Backup**: `spatie/laravel-backup` terjadwal aktif sejak deploy pertama, bukan ditambahkan belakangan.
- [ ] **Monitoring**: Sentry (atau setara) terpasang sebelum trafik harian nyata dimulai, agar error pertama di production tertangkap, bukan baru disadari lewat laporan manual.

---

## 14. Maintenance Guide

- **Bug Fix Workflow:** bug dari dogfooding harian dicatat segera (mis. issue tracker sederhana/catatan), diperbaiki lewat `fix/*` branch, melalui CI sebelum merge — tidak ditambal langsung di `main` tanpa test.
- **Update Dependency:** ditinjau bulanan; `composer outdated` dicek, upgrade dilakukan bertahap (bukan big-bang) khususnya untuk major version Laravel.
- **Database Maintenance:** tinjau index yang jarang terpakai (`pg_stat_user_indexes`) setiap beberapa bulan seiring data bertambah.
- **Backup Verification:** uji restore backup **setiap bulan** — bukan hanya mempercayai proses backup berjalan tanpa pernah dites (Blueprint bagian 23).
- **Performance Monitoring:** tinjau waktu respons Dashboard secara berkala; jika mulai melambat, cek cache hit-rate dan query log sebelum menambah infrastruktur baru.
- **Security Monitoring:** tinjau log channel `security` (percobaan login gagal) setiap sesi maintenance; perbarui dependency dengan CVE diketahui segera, di luar jadwal bulanan biasa jika tingkat keparahan tinggi.
- **Documentation Update:** setiap keputusan baru yang menyimpang dari 6 dokumen acuan dicatat sebagai addendum (bukan mengedit dokumen asal), agar riwayat keputusan tetap terlacak.

---

## 15. Long-Term Development Strategy

Mengikuti kerangka waktu Blueprint bagian 14 (3–5 tahun), diterjemahkan menjadi keputusan implementasi konkret:

| Keputusan | Kapan Layak Dilakukan | Alasan |
|---|---|---|
| **Fitur baru di luar 12 modul FSD** | Hanya setelah v1.0 stabil didogfooding minimal 1–2 bulan penuh | Memastikan siklus inti benar-benar teruji dulu sebelum menambah kompleksitas (mencegah scope creep berulang, Blueprint bagian 13) |
| **Optimasi performa lanjutan** (read-replica, partisi tabel) | Hanya jika ada gejala nyata (waktu respons Dashboard/Search mulai terasa lambat secara konsisten) | Optimasi preventif tanpa data nyata adalah bentuk over-engineering (Blueprint bagian 13) |
| **Memecah modul menjadi service terpisah** | Hanya jika satu modul (mis. Notification Engine) benar-benar menjadi bottleneck terukur pada monolith | Modular monolith sengaja dipilih agar pemecahan ini adalah opsi, bukan kebutuhan mendesak (TDD bagian 2) |
| **API publik diaktifkan penuh** | Saat companion app/mobile (baris berikut) benar-benar mulai dikerjakan, bukan lebih awal | API Resource sudah disiapkan sejak MVP (TDD bagian 31) — mengaktifkan endpoint hanya perlu dilakukan saat ada konsumen nyata |
| **Aplikasi mobile** | Tahap 1: PWA terlebih dahulu (murah, cepat); Tahap 2: mobile Quick-Capture-only jika pain point capture-on-the-go terbukti signifikan dari pemakaian nyata; Tahap 3: aplikasi penuh hanya jika tahap 2 tervalidasi | Blueprint bagian 18 — menghindari investasi besar pada kebutuhan yang belum terbukti |
| **Dukungan multi-user** | Setelah validasi privat/undangan terbatas (Blueprint bagian 14, Tahun 3) menunjukkan kebutuhan bersifat lebih universal, bukan personal semata | Fondasi `user_id`+Policy sudah siap sejak awal (Bagian 3.0/3.1) — keputusan ini murni soal *kapan membuka*, bukan *kapan membangun fondasi* (fondasi sudah ada sejak hari pertama) |
| **Integrasi AI & Automation** | Setelah siklus inti + Review & Reflection matang dan berisi cukup data historis nyata untuk dijadikan dasar saran (mis. AI Review Summary) | AI diposisikan sebagai lapisan bantu di atas sistem deterministik yang sudah bekerja (Blueprint bagian 16) — dibangun terlalu awal berisiko AI "menebak" pola dari data yang belum representatif |

**Prinsip yang mengikat seluruh tabel di atas:** setiap keputusan "kapan" dijawab dengan **bukti pemakaian nyata**, bukan asumsi kebutuhan masa depan — konsisten dengan filosofi dogfooding yang menjadi inti strategi implementasi sejak bagian 1 dokumen ini.

---

## Penutup

Dokumen ini menutup rangkaian tujuh dokumen (Blueprint v1.0, FSD, TDD, Database & Business Rules, UI/UX Spec, dan Implementation Guide ini) menjadi satu jalur eksekusi yang dapat diikuti hari demi hari: mulai dari Sprint 1 (Core Infrastructure) di bagian 8, dengan checklist "sebelum mulai" dan "setelah selesai" yang jelas di setiap modul (bagian 3), sehingga pertanyaan "apa yang saya kerjakan hari ini, dan mengapa urutannya begini" selalu memiliki jawaban eksplisit tanpa perlu keputusan arsitektur baru di tengah proses.
