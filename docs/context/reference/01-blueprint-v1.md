# PERSONAL OS — BLUEPRINT v1.0
### Dokumen Referensi Utama Pengembangan Aplikasi

---

## Catatan Audit & Revisi

Sebelum masuk ke isi blueprint, berikut hasil audit terhadap dokumentasi Tahap 1–4. Beberapa inkonsistensi ditemukan dan diperbaiki dalam dokumen ini:

| # | Temuan | Perbaikan yang Dilakukan |
|---|---|---|
| 1 | Tahap 4 menempatkan "Reminder dasar" di MVP 1 (v0.2), namun diagram *Module Dependency* menyiratkan Notification Engine bergantung pada Task, Project, **dan Habit** — padahal Habit baru dibangun di v0.3. | Diperjelas di blueprint ini: **Notification Engine dipecah menjadi dua lapis** — (1) *Deadline Reminder* (hanya bergantung Task/Project, masuk MVP) dan (2) *Full Notification Engine* (mencakup jadwal Habit + ritual Review, dibangun setelah Habit ada). Ini menghapus kontradiksi urutan pembangunan. |
| 2 | Tahap 2 menyebut Focus Mode sebagai modul tersendiri yang "dipicu dari item mana pun", namun Tahap 3 tidak eksplisit menjelaskan bagaimana Focus Mode direpresentasikan di database. | Diperjelas: Focus Mode **tidak memiliki tabel/entitas sendiri** — ia murni lapisan UI/state sementara di atas Task yang sudah ada. Ini dicatat eksplisit agar tidak muncul kebingungan desain skema saat implementasi. |
| 3 | Tahap 1 menyebut "reminder aktif" sebagai kebutuhan inti, sementara Tahap 4 checklist MVP 0 tidak menyertakan reminder sama sekali. | Ini **bukan kontradiksi**, melainkan urutan bertahap yang disengaja — namun perlu ditulis eksplisit dalam blueprint agar tidak terbaca sebagai requirement yang terlewat. Sudah ditambahkan catatan penjelas di bagian Development Roadmap. |
| 4 | Tahap 2 menempatkan Tagging/Context sebagai "Should Have" dalam tabel MoSCoW, namun di bagian Dependency dan MVP Feature yang sama, Tagging justru disebut sebagai fondasi yang harus dibangun sejak awal. | **Diperbaiki dalam blueprint ini**: Tagging/Context dinaikkan status resminya menjadi **Must Have**, karena logika dependency-nya sendiri sudah menyiratkan itu — ini adalah keputusan desain yang kurang tepat di dokumen asal dan telah dikoreksi. |
| 5 | Tahap 3 merekomendasikan MySQL *atau* PostgreSQL tanpa keputusan final, yang berisiko menunda keputusan teknis sederhana. | Blueprint ini mengambil keputusan final: **PostgreSQL**, dengan alasan dijelaskan di bagian Technology Stack — agar tidak ada keputusan mengambang yang menghambat mulainya development. |
| 6 | Tahap 2 (Out of Scope) menyatakan kolaborasi multi-user dan integrasi eksternal "tidak masuk scope", sementara Tahap 5 diminta membahas strategi jangka panjang untuk hal-hal tersebut. | **Bukan kontradiksi** — bagian ini di blueprint ditulis eksplisit sebagai *rencana masa depan (future-facing)*, terpisah jelas dari scope pengembangan aktif saat ini. |

---

## 1. Executive Summary

Personal OS adalah aplikasi web yang berfungsi sebagai **Personal Operating System / Second Brain** untuk mengelola ide, tugas, proyek, pembelajaran, dan kebiasaan dalam satu sistem terintegrasi. Produk ini menjawab masalah struktural yang dialami penggunanya: bukan kekurangan motivasi atau ide, melainkan kekurangan sistem yang mampu menyatukan proses **menangkap, mengorganisasi, memprioritaskan, mengeksekusi, dan merefleksikan** aktivitas sehari-hari.

Dikembangkan oleh dan untuk satu orang (solo developer, single user) dengan aktivitas yang beragam — mulai dari produksi konten YouTube, pembelajaran bahasa dan software, hingga proyek kreatif pribadi — Personal OS dirancang dengan filosofi **modular monolith berbasis Laravel**, memakai konvensi framework seketat mungkin, dan mengambil keputusan arsitektur yang murah hari ini namun memudahkan pertumbuhan ke skala lebih besar (multi-user, traffic lebih tinggi) di masa depan.

Blueprint ini adalah hasil audit dan penyempurnaan dari empat tahap analisis sebelumnya (Discovery, Feature Planning, Technical Architecture, Development Planning), digabung menjadi satu dokumen rujukan tunggal yang konsisten dan siap dijadikan pegangan sepanjang proses pengembangan.

---

## 2. Product Vision

> **Menjadi sistem operasi pribadi digital yang membantu seseorang berpikir jernih, merencanakan dengan sengaja, dan bertindak konsisten — sehingga setiap ide, tujuan, dan pekerjaan memiliki tempat, arah, dan kemajuan yang terlihat.**

Personal OS bukan "to-do list versi lain", melainkan lapisan berpikir (thinking layer) yang menyatukan seluruh siklus produktivitas pribadi: **Capture → Organize → Prioritize → Execute → Review**. Setiap keputusan produk, fitur, maupun teknis dalam blueprint ini dapat ditelusuri kembali ke satu atau lebih tahap siklus ini.

---

## 3. Requirement Analysis

### 3.1 Problem Statement
Pengguna adalah kreator dan pembelajar aktif dengan banyak minat paralel yang mengalami **kegagalan sistem organisasi**, bukan kegagalan motivasi: ide dan tugas mudah hilang, prioritas antar proyek tidak jelas, waktu tidak terkelola, dan tidak ada mekanisme aktif yang menjaga fokus atau mengingatkan.

### 3.2 Pain Points Utama
Capture yang tidak konsisten, task/deadline terlupakan, prioritas antar proyek tidak jelas, informasi tersebar di banyak aplikasi, progres tidak terlihat sehingga motivasi menurun, dan kesulitan membangun kebiasaan konsisten.

### 3.3 Target Persona
**"The Multi-Passionate Creator/Learner"** — individu yang nyaman dengan teknologi, memiliki banyak ide dan proyek paralel, namun kesulitan menyelesaikan sesuatu karena distraksi antar proyek. Kebutuhan intinya: satu sistem yang dapat dipercaya untuk "mengingat" segalanya, agar kapasitas mentalnya bisa fokus pada berkarya.

### 3.4 Functional & Non-Functional Requirements (Ringkasan)
**Fungsional inti:** quick capture, pengelompokan ke proyek/goal, penentuan prioritas, tampilan "apa yang penting sekarang", target harian–jangka panjang, progres visual, reminder proaktif, penyimpanan referensi, habit tracking, fleksibilitas struktur data untuk aktivitas baru.

**Non-fungsional kunci:** skalabilitas arsitektur (single-user → multi-user), fleksibilitas model data, kecepatan capture (harus terasa instan), keandalan data (tidak boleh hilang), kesederhanaan kognitif antarmuka, keamanan data pribadi, portabilitas data (export/backup mandiri).

### 3.5 Product Principles
Satu sumber kebenaran; capture harus mudah, organisasi bisa menyusul; prioritas di atas daftar panjang; fleksibel tapi terstruktur; progres yang terlihat menjaga motivasi; dibangun untuk berkembang.

---

## 4. Product Architecture & Feature Planning

### 4.1 Modul Inti (Revisi Final)

| Modul | Fungsi | Status Prioritas *(direvisi)* |
|---|---|---|
| Inbox / Capture | Menangkap ide/tugas spontan | Must Have |
| Tasks | Unit kerja harian yang dieksekusi | Must Have |
| Projects & Goals | Struktur proyek dan tujuan + progres | Must Have |
| **Tagging/Context** | Lapisan metadata lintas modul | **Must Have** *(dinaikkan dari "Should Have" — lihat Catatan Audit #4)* |
| Dashboard/Today View | Tampilan "apa yang penting sekarang" | Must Have |
| Deadline Reminder | Reminder dasar berbasis tenggat | Must Have |
| Habit Tracking | Pelacakan kebiasaan berulang | Should Have |
| Knowledge Base / Notes | Referensi dan materi belajar | Should Have |
| Focus Mode | Ruang eksekusi tanpa distraksi *(lapisan UI, bukan entitas data terpisah)* | Should Have |
| Review & Reflection | Ritual peninjauan berkala | Should Have |
| Full Notification Engine | Reminder jadwal Habit + ritual Review | Could Have |
| Search Lintas Modul | Pencarian global terkategorikan | Could Have |

### 4.2 Feature Relationship (Ringkasan)
Inbox adalah pintu masuk satu arah yang mendistribusikan item ke Task, Project, atau Knowledge Base. Dashboard adalah satu-satunya pintu keluar ke pekerjaan harian, mengagregasi Task prioritas, Habit hari ini, dan reminder aktif. Focus Mode menarik satu item aktif dari modul mana pun sebagai lapisan tampilan sementara. Review mengagregasi data historis dari seluruh modul untuk mendukung refleksi berkala.

---

## 5. Information Architecture

```
Personal OS
├── Today (Dashboard)              — titik masuk utama
├── Inbox                          — capture & triase
├── Projects & Goals
│   ├── Goal (berujung / berkelanjutan)
│   └── Project → Task
├── Tasks (all-tasks view)
├── Habits
├── Knowledge Base (Notes & Referensi)
├── Focus Mode (kontekstual)
├── Review (Daily / Weekly / Monthly)
└── Settings (Tags, Notification, Data Export/Backup)
```

Hierarki: **Goal** (arah jangka panjang) → **Project** (cara mencapainya) → **Task** (unit eksekusi harian). **Habit** berdiri sejajar dengan Goal karena sifatnya berkelanjutan, bukan berujung pada satu hasil akhir.

**Navigasi primer** dibatasi maksimal 6 item (Today, Inbox, Projects & Goals, Habits, Knowledge Base, Review) untuk menjaga kesederhanaan kognitif — Task sengaja tidak memiliki item navigasi sendiri, selalu diakses lewat Dashboard atau Project.

---

## 6. User Flow

**Menangkap ide spontan:** ide muncul → buka Inbox → ketik singkat → simpan → kembali ke aktivitas semula (tanpa kategorisasi di titik ini).

**Memulai hari kerja:** buka aplikasi → mendarat di Dashboard → melihat Task prioritas + Habit hari ini → pilih satu item → masuk Focus Mode.

**Triase Inbox:** buka Inbox → per item: jadikan Task / masukkan ke Project / simpan sebagai Note / hapus → Inbox kosong.

**Menyelesaikan proyek konten:** Goal → Project → Task (ide → storyboard → shooting → editing → publish) → progres Goal ter-update otomatis dari penyelesaian Task.

**Review mingguan:** reminder ritual → buka Review > Weekly → ringkasan progres → catatan refleksi → penyesuaian prioritas minggu depan.

---

## 7. Use Case

| Use Case | Deskripsi |
|---|---|
| Menangkap ide cepat | Mencatat ide/tugas tanpa harus menentukan kategori dulu |
| Triase Inbox | Mendistribusikan item Inbox ke modul lain atau menghapusnya |
| Membuat & memecah Goal | Menentukan tujuan lalu merincinya menjadi Project & Task |
| Menandai Task selesai | Memicu update progres Project/Goal secara otomatis |
| Melacak Habit harian | Mencentang kebiasaan yang telah dilakukan |
| Menyimpan referensi belajar | Menambah catatan, opsional ditautkan ke Project |
| Masuk Focus Mode | Bekerja pada satu Task tanpa distraksi item lain |
| Menerima reminder | Notifikasi proaktif berbasis deadline atau jadwal |
| Melakukan Review berkala | Meninjau progres dan menyesuaikan rencana |
| Mencari lintas modul | Menemukan Task/Note/Project via pencarian global |

---

## 8. Database Concept

- Satu database relasional (**PostgreSQL** — keputusan final, lihat bagian Technology Stack) sebagai single source of truth.
- Setiap tabel entitas utama menyertakan `user_id` sejak awal — keputusan skalabilitas paling penting dalam seluruh blueprint ini.
- Soft delete pada entitas utama untuk mendukung keandalan data ("data pribadi tidak boleh hilang").
- Primary key publik berbasis ULID untuk menghindari kebocoran informasi urutan/jumlah data saat berkembang ke API/multi-user.

**Entitas utama:** User, Goal, Project, Task, Habit, HabitLog, Note, InboxItem, Tag (polymorphic), ReviewEntry, NotificationPreference.

**Relasi kunci:** Goal 1—N Project (opsional), Project 1—N Task (opsional — Task juga bisa berdiri bebas), Habit 1—N HabitLog, Tag bersifat polymorphic ke Task/Project/Note/Habit. Relasi Project–Task dan Goal–Project sengaja opsional/nullable untuk menjaga fleksibilitas struktur data terhadap aktivitas baru yang belum terpikirkan.

**Klarifikasi penting (hasil audit #2):** Focus Mode **tidak** memiliki tabel tersendiri — murni state UI sementara di atas Task yang sudah ada.

---

## 9. System Architecture

Sistem berbentuk **modular monolith** dalam satu aplikasi Laravel, dibagi menjadi tiga zona: **Presentation** (Blade + Livewire), **Application/Domain** (Action/Service per modul: Inbox, Tasks, Projects, Habits, KnowledgeBase, Notification, Review, Shared), dan **Infrastructure** (database, Redis untuk cache & queue, object storage).

Setiap modul mengikuti lapisan konsisten: Controller/Livewire → Form Request → Action/Service → Model (Eloquent) → Policy → Event/Listener untuk efek samping lintas modul (mis. `TaskCompleted` yang memicu update progres Project dan pencatatan riwayat Review).

**Alasan modular monolith dibanding microservices:** microservices menambah kompleksitas operasional (banyak deployment, orkestrasi, titik kegagalan jaringan) yang tidak sepadan untuk solo developer dan skala pengguna saat ini, sambil tetap memungkinkan pemisahan modul menjadi service terpisah nanti jika benar-benar diperlukan.

---

## 10. UI/UX Recommendation

**Prinsip desain:** clarity over density, progressive disclosure, konsistensi pola interaksi lintas modul, calm technology (notifikasi membantu, bukan mengganggu), fleksibel dengan default yang jelas.

**Rekomendasi kunci:**
1. Capture maksimal 1–2 langkah, dapat diakses dari halaman mana pun.
2. Dashboard adalah landing page default — menyaring, bukan menampilkan semua data mentah.
3. Progres divisualisasikan (progress bar, streak counter), bukan hanya dinarasikan teks.
4. Sedikit gesekan untuk hal spontan (capture), sedikit lebih deliberate untuk hal permanen (mengubah struktur Goal/Project) — mencegah struktur berantakan akibat perubahan impulsif.
5. Review harus punya tempat yang jelas dan mudah dijangkau, agar benar-benar dipakai rutin — bukan terlupakan seperti masalah awal di Requirement Analysis.

---

## 11. Technology Stack

| Layer | Teknologi | Alasan |
|---|---|---|
| Backend | Laravel (versi LTS/stabil terbaru) | Konvensi kuat, ekosistem matang, cocok solo developer |
| Frontend interaktif | Livewire + Alpine.js | Menghindari kompleksitas SPA + API terpisah |
| Styling | Tailwind CSS | Mempercepat styling konsisten tanpa design system dari nol |
| **Database** | **PostgreSQL** *(keputusan final — hasil audit #5)* | Dukungan penuh di Laravel, fitur data lanjutan (full-text search, JSONB untuk atribut fleksibel di masa depan) lebih matang dibanding MySQL untuk kebutuhan jangka panjang aplikasi ini |
| Cache & Queue | Redis | Satu komponen melayani dua kebutuhan, mengurangi jumlah infrastruktur yang dikelola sendiri |
| Queue Monitoring | Laravel Horizon | Visibilitas job tanpa tooling tambahan |
| Storage | Local disk (awal) → S3-compatible (mis. Cloudflare R2) saat berkembang | Migrasi hanya soal konfigurasi via Laravel Filesystem abstraction |
| Autentikasi | Laravel Breeze/Fortify + Sanctum (disiapkan) | Konvensi standar, minim maintenance, siap API di masa depan |
| Backup | spatie/laravel-backup (atau setara) | Matang dan terintegrasi baik dengan Laravel |
| Hosting | Managed VPS dengan panel deployment (mis. Laravel Forge) / PaaS pendukung Laravel | Mengurangi beban operasional DevOps solo developer |
| Error Tracking | Sentry (ditambahkan begitu pemakaian harian stabil) | Deteksi dini masalah produksi tanpa monitoring custom |

---

## 12. Development Roadmap

### 12.1 Strategi
Pendekatan **iteratif & vertikal** (thin slices dari seluruh siklus, bukan modul-per-modul horizontal), agar aplikasi dapat mulai "dipakai sendiri" (dogfooding) sedini mungkin.

### 12.2 Versi & Milestone

| Versi | Fokus | Milestone |
|---|---|---|
| v0.1 | MVP 0 — Skeleton: Auth, Inbox, Task, Dashboard sederhana | Milestone 1: "Bisa Dipakai" |
| v0.2 | MVP 1 — Projects & Goals, **Deadline Reminder**, Tagging, triase penuh | Milestone 1: "Bisa Dipakai" |
| v0.3 | Habit Tracking | Milestone 2: "Habit & Fokus" |
| v0.4 | Knowledge Base + Focus Mode | Milestone 2: "Habit & Fokus" |
| v0.5 | Review & Reflection (harian + mingguan) | Milestone 3: "Reflektif" |
| v0.6 | Search lintas modul + **Full Notification Engine** (jadwal Habit + ritual Review) | Milestone 4: "Matang & Stabil" |
| v1.0 | Stabilisasi, hardening keamanan/performa/backup | Milestone 4: "Matang & Stabil" |

**Catatan penting (hasil audit #1 & #3):** reminder dipecah dua lapis secara sengaja — *Deadline Reminder* masuk MVP karena hanya bergantung pada Task/Project yang sudah ada di v0.2; *Full Notification Engine* (mencakup jadwal Habit dan reminder ritual Review) baru dibangun di v0.6 setelah Habit dan Review benar-benar ada datanya. Ini menghapus kontradiksi dependency yang sempat muncul di dokumen Tahap 4.

Estimasi kasar menuju v1.0: **± 3–4 bulan** dengan ritme kerja paruh waktu (± 8–10 jam efektif/minggu) — rentang lebar sengaja diberikan karena risiko utama solo project adalah konsistensi waktu, bukan kompleksitas teknis.

### 12.3 Praktik Pendukung
Sprint mingguan (3–5 task konkret), trunk-based Git workflow sederhana (`main` selalu stabil + `feature/*` berumur pendek), testing difokuskan pada Action/Service berisiko tinggi (bukan cakupan 100%), dan dogfooding harian dimulai sejak MVP 0 selesai.

---

## 13. Risiko Pengembangan (Diperluas) & Mitigasi

| Risiko | Dampak | Mitigasi |
|---|---|---|
| Kehilangan momentum di tengah jalan | Tinggi | Sprint mingguan kecil + rilis MVP 0 secepat mungkin sebagai "kemenangan cepat" |
| Scope creep (menambah Future Feature sebelum MVP stabil) | Tinggi | Rujuk kembali ke tabel MoSCoW (bagian 4.1) setiap muncul ide fitur baru |
| Over-engineering fondasi teknis | Sedang | Batasi waktu fase arsitektur sebelum mulai coding fitur nyata |
| Utang teknis menumpuk tanpa disadari (tidak ada tim yang menegur) | Sedang | Catatan technical debt eksplisit, ditinjau rutin saat maintenance bulanan |
| Kelelahan solo developer | Sedang | Sprint realistis sesuai waktu tersedia, bukan target ideal yang memaksakan diri |
| Perubahan kebutuhan seiring pemakaian nyata | Sedang | Struktur data fleksibel (relasi opsional) + Review pribadi berkala terhadap relevansi fitur |
| Struktur data terlalu kaku untuk aktivitas baru | Sedang | Relasi Goal–Project–Task dibuat opsional/nullable sejak desain awal (bagian 8) |
| Reminder/notifikasi terasa mengganggu, bukan membantu | Rendah–Sedang | Kontrol granular per jenis reminder; prinsip Calm Technology (bagian 10) |
| Ketergantungan pada satu sistem tanpa mekanisme backup/export | Sedang | Backup otomatis berlapis + fitur export data mandiri (dibahas di bagian 15) |
| **(Baru)** Ketergantungan berlebihan pada satu developer untuk seluruh siklus hidup produk | Sedang | Dokumentasi teknis dan ADR ringan dijaga rapi (Tahap 4, bagian Documentation Strategy) agar produk tetap dapat dilanjutkan meski ada jeda panjang pengembangan |

---

## 14. Saran Pengembangan Jangka Panjang (3–5 Tahun ke Depan)

Bagian ini bersifat **future-facing** — bukan bagian dari scope pengembangan aktif saat ini (lihat Catatan Audit #6), melainkan arah yang perlu dipikirkan sejak awal agar keputusan hari ini tidak mempersempit pilihan di masa depan.

1. **Tahun 1**: fokus penuh pada penggunaan personal yang matang (v1.0 hingga v1.x) — memastikan siklus Capture–Organize–Prioritize–Execute–Review benar-benar teruji lewat pemakaian harian nyata.
2. **Tahun 2**: mulai eksplorasi fitur analitik jangka panjang (tren produktivitas, pola konsistensi habit) dan penghalusan pengalaman lintas perangkat (mis. progressive web app sebelum mobile native penuh).
3. **Tahun 3**: pertimbangkan pembukaan produk secara terbatas ke pengguna lain (privat/undangan) untuk memvalidasi apakah kebutuhan yang dipecahkan bersifat personal semata atau lebih universal.
4. **Tahun 4–5**: jika validasi tahun 3 positif, pertimbangkan transisi bertahap ke produk multi-user penuh dengan model bisnis yang jelas (lihat bagian 19), didukung oleh fondasi arsitektur yang sejak awal sudah dirancang siap untuk itu (bagian 8 & 9).

Prinsip yang dipegang di seluruh jangka waktu ini: **jangan membangun untuk skala yang belum tentu terjadi**, tetapi jangan pula mengambil keputusan hari ini yang menutup jalan menuju skala tersebut.

---

## 15. Future Scalability Plan

1. **Data**: struktur `user_id` di setiap tabel dan Policy berbasis kepemilikan sejak hari pertama (bagian 8) — investasi skalabilitas termurah dan terpenting.
2. **Aplikasi**: modular monolith memungkinkan modul tertentu (mis. Notification Engine) dipisah menjadi service independen jika beban meningkat signifikan, tanpa merombak seluruh aplikasi.
3. **Infrastruktur**: Redis dan storage dirancang agar dapat di-scale independen (instance terpisah) tanpa mengubah kode aplikasi; database dapat dipisah menjadi read-replica saat kebutuhan baca meningkat (khas aplikasi dengan Dashboard yang sering diakses).
4. **Fitur**: fitur "Future Feature" (context-aware suggestion, someday/maybe list, analitik tren) diletakkan sebagai lapisan tambahan di atas fondasi yang sudah stabil, bukan dipaksakan masuk sebelum fondasi inti matang.
5. **Portabilitas data**: fitur export mandiri (JSON/CSV) dijaga tetap tersedia di setiap tahap perkembangan, sebagai jaring pengaman terhadap ketergantungan berlebihan pada satu sistem.

---

## 16. Integrasi AI dan Automation

*(Rencana masa depan — tidak masuk scope MVP maupun v1.0)*

1. **AI sebagai asisten triase Inbox**: menyarankan kategori (Task/Note/Project) untuk item Inbox berdasarkan pola penggunaan sebelumnya — mempercepat proses triase tanpa menggantikan keputusan akhir pengguna.
2. **AI sebagai pendukung prioritas**: memberi saran urutan pengerjaan Task berdasarkan deadline, riwayat penyelesaian, dan konteks — bersifat *saran*, bukan keputusan otomatis, agar kontrol tetap di tangan pengguna (sejalan prinsip produk di bagian 3.5).
3. **Automation berbasis Event**: memanfaatkan Event/Listener yang sudah ada di arsitektur (bagian 9) untuk automasi sederhana, misalnya auto-archive Project yang tidak tersentuh dalam waktu lama (idle detection — sudah disinggung sebagai future feature di dokumen Tahap 2).
4. **AI untuk Review & Reflection**: merangkum progres periodik menjadi narasi ringkas, membantu pengguna merefleksikan pola kerja tanpa harus membaca seluruh data mentah.

**Prinsip penting:** AI diposisikan sebagai *lapisan bantu* di atas sistem yang sudah bekerja secara deterministik — bukan pengganti struktur data dan alur inti yang sudah dirancang di bagian 8–9. Ini menjaga aplikasi tetap dapat diandalkan meski komponen AI mengalami kegagalan/perubahan di kemudian hari.

---

## 17. Integrasi Layanan Pihak Ketiga

*(Rencana masa depan, di luar scope MVP — lihat Out of Scope Tahap 2)*

1. **Kalender eksternal** (Google Calendar, dsb.) — sinkronisasi dua arah untuk Task berdeadline dan jadwal Habit, agar pengguna tidak perlu mengecek dua sistem terpisah.
2. **Platform konten** (mis. YouTube Studio) — kemungkinan menarik status publish video sebagai pemicu otomatis penyelesaian Task/Project terkait produksi konten.
3. **Layanan penyimpanan cloud** (Google Drive, dsb.) — menautkan file besar (footage, source file desain) ke Knowledge Base tanpa perlu mengunggah ulang ke storage aplikasi.
4. **Layanan notifikasi eksternal** (mis. push notification mobile, WhatsApp/Telegram bot) — memperluas jangkauan reminder di luar aplikasi web.

**Prinsip integrasi:** setiap integrasi pihak ketiga bersifat *opsional dan dapat dimatikan*, agar aplikasi tetap berfungsi penuh secara mandiri tanpa ketergantungan pada layanan eksternal manapun — sejalan dengan prinsip keandalan data di bagian 3.4.

---

## 18. Mobile Application Strategy

*(Rencana masa depan — aplikasi mobile native secara eksplisit di luar scope MVP, lihat Tahap 2: Out of Scope)*

1. **Tahap transisi pertama**: menjadikan aplikasi web sebagai **Progressive Web App (PWA)** — memberi pengalaman mirip aplikasi mobile (dapat "diinstal", bekerja offline sebagian) tanpa perlu membangun aplikasi native terpisah.
2. **Tahap lanjutan**: jika kebutuhan capture-on-the-go terbukti krusial (khas pain point "ide muncul tiba-tiba" dari Requirement Analysis), pertimbangkan aplikasi mobile ringan yang **fokus hanya pada Quick Capture** — bukan replikasi seluruh fitur desktop — memanfaatkan API yang sudah disiapkan sejak Tahap 3 (Sanctum + `routes/api.php`).
3. **Tahap penuh**: aplikasi mobile lengkap (menyamai fitur web) hanya dipertimbangkan setelah validasi kebutuhan nyata dari tahap 2, untuk menghindari investasi besar pada fitur yang belum tentu dibutuhkan.

---

## 19. Multi User & Collaboration Strategy

*(Rencana masa depan — di luar scope MVP dan v1.0)*

1. **Fondasi sudah disiapkan sejak awal**: `user_id` di setiap tabel dan Policy berbasis kepemilikan (bagian 8–9) berarti transisi ke multi-user tidak memerlukan migrasi skema besar — hanya perlu membuka pendaftaran akun baru dan memastikan isolasi data antar-user benar-benar teruji.
2. **Permission granular** (role-based, mis. via spatie/laravel-permission) baru diperlukan jika kolaborasi tim/berbagi data antar-user mulai dibutuhkan — sengaja tidak dibangun lebih awal untuk menghindari over-engineering (lihat Tahap 3, Permission Strategy).
3. **Model kolaborasi** (jika suatu saat diarahkan ke tim, bukan lagi murni personal): berbagi Project tertentu antar-user, dengan Task tetap memiliki satu pemilik utama — mempertahankan filosofi "personal" inti sambil membuka ruang kolaboratif terbatas.
4. **Keputusan strategis**: transisi ke multi-user sebaiknya didahului validasi nyata (lihat bagian 14, Tahun 3) — bukan dibangun preventif tanpa bukti kebutuhan, sejalan prinsip menghindari scope creep di bagian 13.

---

## 20. Kesimpulan Akhir

Personal OS, sebagaimana dirumuskan dalam blueprint ini, adalah jawaban terhadap masalah yang bersifat struktural: kebutuhan akan satu sistem yang mampu menyatukan proses menangkap, mengorganisasi, memprioritaskan, mengeksekusi, dan merefleksikan aktivitas hidup — bukan sekadar daftar tugas yang menumpuk.

Proses audit menyeluruh terhadap Tahap 1–4 mengungkap beberapa keputusan yang perlu diperjelas atau dikoreksi (lihat Catatan Audit di awal dokumen), yang paling signifikan adalah: (1) penetapan status **Must Have** yang konsisten untuk Tagging/Context sebagai fondasi lintas modul, (2) pemisahan **Deadline Reminder** dan **Full Notification Engine** menjadi dua lapis pembangunan yang berbeda untuk menghapus kontradiksi dependency, (3) klarifikasi bahwa **Focus Mode** adalah lapisan UI, bukan entitas data, dan (4) keputusan final basis data ke **PostgreSQL** untuk menghindari keputusan teknis yang mengambang.

Dengan fondasi produk yang jelas (Tahap 1), struktur fitur yang terarah (Tahap 2), arsitektur teknis yang realistis untuk solo developer namun siap tumbuh (Tahap 3), serta roadmap pengembangan yang bertahap dan tidak memaksakan diri (Tahap 4), blueprint ini menyatukan seluruhnya menjadi satu rujukan tunggal yang konsisten.

Keberhasilan produk ini pada akhirnya tidak diukur dari banyaknya fitur yang dibangun, melainkan dari seberapa mulus siklus **Capture → Organize → Prioritize → Execute → Review** dapat dijalankan setiap hari, dan seberapa jujur sistem ini merefleksikan kemajuan penggunanya dari waktu ke waktu. Blueprint v1.0 ini siap dijadikan referensi utama pengembangan, dengan struktur yang cukup jelas untuk memulai, dan cukup fleksibel untuk terus disempurnakan seiring pemakaian nyata.
