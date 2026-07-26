# PERSONAL OS — FUNCTIONAL SPECIFICATION DOCUMENT (FSD)
### Acuan Implementasi Fitur — Berdasarkan Blueprint v1.0

---

## Catatan Konsistensi terhadap Blueprint v1.0

Sebelum masuk ke spesifikasi, satu inkonsistensi kecil ditemukan dan diperbaiki di sini:

**Temuan:** Blueprint v1.0 menyebut `NotificationPreference` sebagai entitas di Database Concept (bagian 8), namun tidak pernah dijelaskan bagian mana yang memilikinya — Deadline Reminder atau Full Notification Engine.
**Perbaikan:** `NotificationPreference` ditetapkan sebagai entitas milik **Shared/Settings**, dibaca oleh kedua modul (Deadline Reminder dan Full Notification Engine), bukan dimiliki eksklusif oleh salah satunya. Ini konsisten dengan prinsip Blueprint bahwa reminder dipecah dua lapis namun preferensi pengguna harus terpusat, bukan terduplikasi.

Tidak ditemukan inkonsistensi lain yang mengharuskan perubahan visi, arsitektur, atau keputusan desain — seluruh spesifikasi di bawah ini konsisten dengan Blueprint v1.0.

---

## Cara Membaca Dokumen Ini

Setiap **Modul** dipecah menjadi satu atau lebih **Fitur**. Setiap Fitur dispesifikasikan lengkap dengan 28 dimensi yang diminta. Untuk dimensi yang nilainya sama di seluruh fitur dalam satu modul (misalnya Permission Rules yang selalu "hanya pemilik data"), ditulis sekali di level modul untuk menghindari pengulangan yang tidak perlu, dan ditandai *"(lihat level modul)"* di level fitur.

---

# MODUL 1 — INBOX / CAPTURE

**Tujuan Modul:** Menjadi satu-satunya pintu masuk untuk seluruh ide, tugas, dan informasi spontan, dengan friksi seminimal mungkin.
**Permasalahan yang Diselesaikan:** Ide/tugas yang muncul tiba-tiba sering lupa dicatat atau tercatat di tempat berbeda-beda (Requirement Analysis, Blueprint bagian 3.2).
**Actor:** User (pemilik data — single actor di seluruh modul, sesuai Blueprint bagian 8 & 19).
**Permission Rules (level modul):** User hanya dapat mengakses, mengubah, dan menghapus `InboxItem` miliknya sendiri (`inbox_item.user_id === auth()->id()`).
**Dependencies (level modul):** Auth/User (fondasi).
**Relationship dengan modul lain:** Inbox mendistribusikan data ke Tasks, Projects & Goals, dan Knowledge Base saat proses triase; tidak menyimpan data secara permanen setelah ditriase.

## 1.1 Fitur: Quick Capture

- **Functional Requirements:** User dapat menambahkan `InboxItem` berupa teks bebas dari halaman mana pun dalam aplikasi, dalam maksimal 1–2 langkah interaksi.
- **Business Objectives:** Menjamin tidak ada ide yang hilang karena proses pencatatan yang rumit; menjadi fondasi kepercayaan pengguna terhadap sistem ("sistem yang bisa dipercaya untuk mengingat segalanya").
- **Workflow:** User memicu form capture (tombol global) → mengetik teks → submit → item tersimpan dengan status `unprocessed` → form tertutup otomatis, user kembali ke konteks sebelumnya.
- **User Flow:** Ide muncul → buka shortcut capture → ketik → simpan → lanjut aktivitas semula (lihat Blueprint bagian 6).
- **Use Case:** "Menangkap ide cepat" (Blueprint bagian 7).
- **Trigger:** User membuka form capture (tombol global/shortcut).
- **Preconditions:** User sudah login.
- **Postconditions:** Satu `InboxItem` baru tersimpan dengan status `unprocessed`, timestamp `created_at` tercatat.
- **Business Rules:**
  - Teks capture tidak boleh kosong.
  - Tidak ada kategorisasi wajib saat capture — kategorisasi terjadi di proses Triage, bukan di titik ini (prinsip "capture harus mudah, organisasi bisa menyusul").
- **Validation Rules:** Panjang teks minimum 1 karakter (non-whitespace); panjang maksimum dibatasi wajar (mis. 5000 karakter) untuk mencegah penyalahgunaan sebagai dokumen panjang (dokumen panjang seharusnya masuk Knowledge Base).
- **Edge Cases:**
  - User submit teks kosong/hanya spasi → ditolak dengan pesan validasi, form tetap terbuka agar teks tidak hilang.
  - User menutup form sebelum submit → teks yang sudah diketik hilang (tidak ada draft otomatis pada versi awal — dicatat sebagai Future Enhancement).
- **Exception Handling:** Kegagalan simpan (mis. koneksi terputus) menampilkan pesan error dan **mempertahankan teks di form** agar user dapat mencoba ulang tanpa kehilangan input.
- **State Machine / Status Lifecycle:** `unprocessed → processed` (lihat Fitur Inbox Triage) atau `unprocessed → discarded` (dihapus langsung tanpa ditriase).
- **Notification Behavior:** Tidak ada notifikasi keluar dari fitur ini.
- **Reminder Behavior:** Tidak berlaku.
- **Search Behavior:** `InboxItem` yang belum diproses ikut termasuk dalam index pencarian global (Modul Search) berdasarkan isi teksnya.
- **Filtering:** Inbox dapat difilter berdasarkan status (`unprocessed`/`processed`) dan rentang tanggal capture.
- **Sorting:** Default: terbaru di atas (`created_at DESC`).
- **Activity Log:** Pencatatan waktu pembuatan (`created_at`) cukup; tidak memerlukan log granular tambahan karena sifatnya transien.
- **Audit Trail:** Tidak diperlukan level detail tinggi untuk fitur ini — cukup timestamp bawaan.
- **Future Enhancement:** Draft otomatis (auto-save saat mengetik), capture via voice-to-text, capture dari luar aplikasi (browser extension/quick-add widget — terkait Mobile Application Strategy Blueprint bagian 18).

## 1.2 Fitur: Inbox Triage

- **Functional Requirements:** User dapat mengubah satu `InboxItem` menjadi Task, Project, atau Note, atau menghapusnya, dalam satu aksi per item.
- **Business Objectives:** Memastikan Inbox tidak menjadi "tempat sampah digital" — setiap item pada akhirnya memiliki tujuan yang jelas dalam sistem.
- **Workflow:** User membuka Inbox → memilih satu item → memilih aksi ("Jadikan Task"/"Jadikan Note"/"Masukkan ke Project"/"Hapus") → sistem mengonversi data → item ditandai `processed` atau `discarded`.
- **User Flow:** Buka Inbox → per item pilih aksi → Inbox berangsur kosong (Blueprint bagian 6).
- **Use Case:** "Triase Inbox" (Blueprint bagian 7).
- **Trigger:** User memilih aksi triase pada sebuah item.
- **Preconditions:** `InboxItem` berstatus `unprocessed` dan dimiliki user yang login.
- **Postconditions:** Entitas baru (Task/Note, atau relasi ke Project) tercipta dengan teks asal sebagai isi awal; `InboxItem` asal berstatus `processed` dan disembunyikan dari tampilan default Inbox (tidak dihapus permanen — untuk audit/riwayat).
- **Business Rules:**
  - Satu `InboxItem` hanya dapat ditriase menjadi satu entitas tujuan (tidak bisa digandakan menjadi Task sekaligus Note dari aksi yang sama).
  - Setelah ditriase, `InboxItem` asal bersifat read-only (arsip), tidak dapat diedit ulang isinya.
- **Validation Rules:** Aksi "Masukkan ke Project" mewajibkan user memilih Project tujuan yang valid (harus sudah ada atau dibuat baru di titik ini).
- **Edge Cases:**
  - User menghapus item yang ternyata masih dibutuhkan → tidak ada undo pada versi awal (dicatat sebagai risiko, mitigasi: soft delete di level database sesuai Blueprint bagian 8, sehingga masih bisa dipulihkan lewat database jika benar-benar diperlukan, meski belum ada UI untuk itu di MVP).
  - Item Inbox sangat banyak menumpuk (backlog besar) → disediakan aksi "bulk discard" sebagai Future Enhancement, bukan MVP.
- **Exception Handling:** Jika konversi ke Task/Project gagal di tengah proses (mis. constraint database), `InboxItem` tetap `unprocessed` dan user diberi pesan error yang jelas — tidak boleh ada state di mana item hilang tanpa entitas tujuan tercipta.
- **State Machine:**
  ```
  unprocessed → processed   (dikonversi ke Task/Note/Project)
  unprocessed → discarded   (dihapus tanpa dikonversi)
  ```
  Status `processed` dan `discarded` bersifat final (tidak ada transisi kembali ke `unprocessed`).
- **Notification Behavior:** Tidak ada.
- **Reminder Behavior:** *(Future Enhancement)* reminder pasif jika Inbox tidak ditriase dalam waktu lama (mis. lebih dari 3 hari), untuk mencegah backlog menumpuk tanpa disadari — belum termasuk MVP.
- **Permission Rules:** (lihat level modul).
- **Search Behavior:** Item `processed`/`discarded` tetap dapat dicari untuk keperluan audit historis, namun tidak muncul di tampilan Inbox aktif.
- **Filtering/Sorting:** (lihat Fitur Quick Capture — filter status berlaku sama).
- **Activity Log:** Mencatat kapan triase dilakukan dan menjadi entitas apa (`processed_at`, `converted_to_type`, `converted_to_id`).
- **Audit Trail:** `InboxItem` asal disimpan permanen (soft delete) sebagai jejak riwayat capture, meski sudah tidak aktif — mendukung prinsip keandalan data dari Blueprint bagian 3.4.
- **Future Enhancement:** Bulk triage, saran kategori otomatis berbasis AI (Blueprint bagian 16, poin 1), reminder Inbox menumpuk.

---

# MODUL 2 — TASKS

**Tujuan Modul:** Menjadi unit eksekusi harian terkecil dalam sistem.
**Permasalahan yang Diselesaikan:** Tugas yang harus dikerjakan sering terlupakan; sulit menentukan prioritas antar pekerjaan.
**Actor:** User.
**Permission Rules (level modul):** User hanya dapat mengakses/mengubah Task miliknya sendiri.
**Dependencies:** Auth/User, Tagging/Context (Must Have — Blueprint bagian 4.1).
**Relationship dengan modul lain:** Task dapat dimiliki oleh Project (opsional); menjadi sumber data utama Dashboard; memicu update progres Project/Goal saat statusnya berubah; menjadi sumber Deadline Reminder; dikonsumsi oleh Focus Mode sebagai item aktif.

## 2.1 Fitur: Task Creation & Editing

- **Functional Requirements:** User dapat membuat Task baru (langsung atau dari hasil triase Inbox) dengan atribut: judul, deskripsi opsional, prioritas, deadline opsional, Project terkait opsional, dan tag.
- **Business Objectives:** Memberi struktur minimal namun cukup pada setiap pekerjaan agar dapat diprioritaskan dan dilacak.
- **Workflow:** User membuka form Task baru (dari Dashboard, Project, atau hasil triase) → mengisi atribut → simpan → Task berstatus `todo`.
- **User Flow:** Buat Task langsung dari Dashboard/Project, atau otomatis dari Inbox Triage.
- **Use Case:** Terkait use case "Menandai Task selesai" di fitur berikutnya, serta pembuatan Task manual.
- **Trigger:** Aksi "Tambah Task" atau hasil konversi Inbox Triage.
- **Preconditions:** User login; jika `project_id` diisi, Project tersebut harus milik user yang sama.
- **Postconditions:** Task baru tersimpan dengan status `todo`.
- **Business Rules:** Judul Task wajib diisi; deskripsi, deadline, Project, dan tag bersifat opsional (menjaga fleksibilitas struktur data sesuai Blueprint bagian 8).
- **Validation Rules:** Judul minimal 1 karakter; jika deadline diisi, harus berupa tanggal valid (boleh di masa lalu — untuk mengakomodasi pencatatan retroaktif, namun ditandai visual sebagai "terlambat").
- **Edge Cases:** Task dibuat tanpa Project maupun Goal (berdiri bebas) → tetap valid, tidak dipaksa punya induk (Blueprint bagian 8: relasi opsional).
- **Exception Handling:** Jika `project_id` yang dipilih ternyata sudah dihapus (race condition), tampilkan pesan error dan minta user memilih ulang atau membiarkan Task berdiri bebas.
- **State Machine / Status Lifecycle:** `todo → in_progress → done`, dengan opsi `todo/in_progress → archived` (dibatalkan tanpa dianggap selesai). Detail transisi lengkap ada di Fitur 2.2.
- **Notification Behavior:** Tidak ada saat pembuatan/edit biasa.
- **Reminder Behavior:** Jika deadline diisi, otomatis terdaftar ke Modul Deadline Reminder.
- **Permission Rules:** (lihat level modul).
- **Search Behavior:** Judul dan deskripsi Task ter-index untuk pencarian global.
- **Filtering:** Berdasarkan status, prioritas, Project, tag, dan rentang deadline.
- **Sorting:** Default: prioritas tinggi dan deadline terdekat di atas.
- **Activity Log:** Mencatat `created_at`, `updated_at`, dan riwayat perubahan atribut penting (deadline, prioritas) untuk keperluan Review.
- **Audit Trail:** Perubahan Project terkait (`project_id`) dicatat riwayatnya agar progres Project dapat direkonsiliasi jika Task dipindah antar-Project.
- **Future Enhancement:** Sub-task/checklist di dalam satu Task, estimasi durasi pengerjaan, Task berulang (recurring task) — dicatat terpisah dari Habit karena sifat "task berulang" berbeda dari kebiasaan (lihat catatan di Modul 7).

## 2.2 Fitur: Task Completion & Status Management

- **Functional Requirements:** User dapat mengubah status Task antara `todo`, `in_progress`, `done`, dan `archived`.
- **Business Objectives:** Menyediakan sinyal progres yang akurat untuk Project/Goal dan Dashboard.
- **Workflow:** User menandai Task selesai (dari Dashboard, Project, atau Focus Mode) → status berubah menjadi `done` → event `TaskCompleted` di-dispatch → listener memperbarui progres Project terkait (jika ada) dan mencatat riwayat untuk Review.
- **User Flow:** Lihat Task di Dashboard/Focus Mode → tandai selesai → progres Project ter-update otomatis (Blueprint bagian 6).
- **Use Case:** "Menandai Task selesai" (Blueprint bagian 7).
- **Trigger:** Aksi user menandai status, atau otomatis dari Focus Mode saat sesi ditutup dengan status selesai.
- **Preconditions:** Task berstatus bukan `done` (untuk transisi ke `done`) dan dimiliki user yang login.
- **Postconditions:** Status Task ter-update; jika Task terhubung ke Project, progres Project dihitung ulang; entri riwayat untuk Review tercatat.
- **Business Rules:**
  - Task yang sudah `done` dapat dibuka kembali (`reopen`) menjadi `todo` jika ternyata belum benar-benar selesai — transisi ini valid dan tidak dianggap kesalahan sistem.
  - Task berstatus `archived` dianggap final dan tidak dihitung dalam progres Project (berbeda dari `done`, yang dihitung sebagai kemajuan).
- **Validation Rules:** Transisi status harus mengikuti State Machine di bawah — tidak boleh melompat status secara tidak logis di level sistem (meski secara bisnis semua transisi manual oleh user diperbolehkan selama sesuai diagram).
- **Edge Cases:**
  - Task dalam Project yang sudah dihapus/di-archive ditandai selesai → tetap diperbolehkan (menyelesaikan pekerjaan tersisa), namun tidak memengaruhi progres Project yang sudah non-aktif.
  - Task tanpa Project ditandai selesai → tidak ada progres agregat yang perlu diperbarui, hanya dicatat untuk Review personal.
- **Exception Handling:** Kegagalan menghitung ulang progres Project (mis. error saat agregasi) tidak boleh membatalkan perubahan status Task itu sendiri — status Task tetap tersimpan, sementara recalculation progres di-retry secara asinkron melalui queue.
- **State Machine:**
  ```
  todo ──────► in_progress ──────► done
   │                │                │
   │                │                └──► (reopen) ──► todo
   └────────────────┴──────────────────► archived
  ```
- **Status Lifecycle:** `todo` (belum dikerjakan) → `in_progress` (sedang dikerjakan, opsional dipakai) → `done` (selesai, dihitung sebagai progres) → dapat kembali ke `todo` via reopen. `archived` adalah status final terpisah untuk Task yang dibatalkan/tidak relevan lagi.
- **Notification Behavior:** Tidak ada notifikasi keluar wajib; *(Future Enhancement)* notifikasi motivasi ringan saat menyelesaikan milestone tertentu (mis. "10 task selesai minggu ini").
- **Reminder Behavior:** Task yang statusnya `done`/`archived` otomatis dihapus dari antrian Deadline Reminder aktif.
- **Permission Rules:** (lihat level modul).
- **Search Behavior:** Task tetap dapat dicari terlepas dari statusnya, namun tampilan default (all-tasks view maupun Dashboard) menyembunyikan `done`/`archived` kecuali difilter eksplisit.
- **Filtering:** Berdasarkan status secara eksplisit (mis. "tampilkan hanya selesai minggu ini" — berguna untuk Review).
- **Sorting:** Task `done` diurutkan berdasarkan `completed_at DESC` saat ditinjau di Review.
- **Activity Log:** Setiap perubahan status dicatat dengan timestamp (`status_changed_at`, `previous_status`, `new_status`).
- **Audit Trail:** Riwayat penuh perubahan status per Task disimpan untuk mendukung Review & Reflection (bagian data historis yang diagregasi).
- **Future Enhancement:** Statistik waktu penyelesaian rata-rata per Task/Project, notifikasi milestone, integrasi dengan AI Review (Blueprint bagian 16, poin 4).

---

# MODUL 3 — PROJECTS & GOALS

**Tujuan Modul:** Menyediakan struktur jangka menengah (Project) dan jangka panjang (Goal) sebagai wadah dan arah bagi Task.
**Permasalahan yang Diselesaikan:** Banyak tujuan dan proyek berjalan bersamaan tanpa urutan dan progres yang jelas.
**Actor:** User.
**Dependencies:** Auth/User, Tasks (untuk perhitungan progres), Tagging/Context.
**Relationship dengan modul lain:** Project menaungi Task; Goal menaungi Project (opsional); Dashboard menampilkan progres agregat; Knowledge Base dapat ditautkan ke Project; Review mengagregasi progres Goal/Project secara periodik.

## 3.1 Fitur: Goal Management

- **Functional Requirements:** User dapat membuat Goal dengan tipe **berujung** (memiliki definisi "selesai" yang jelas) atau **berkelanjutan** (tidak pernah benar-benar "selesai", mis. belajar bahasa).
- **Business Objectives:** Memberi arah jangka panjang yang menyatukan beberapa Project, serta mendukung visibilitas progres untuk menjaga motivasi (Blueprint bagian 3.5).
- **Workflow:** User membuat Goal → memilih tipe (berujung/berkelanjutan) → (opsional) menautkan Project yang sudah ada atau membuat Project baru di bawahnya.
- **User Flow:** Lihat Blueprint bagian 6 (alur proyek konten: Goal → Project → Task).
- **Use Case:** "Membuat & memecah Goal" (Blueprint bagian 7).
- **Trigger:** Aksi user "Buat Goal baru".
- **Preconditions:** User login.
- **Postconditions:** Goal baru tersimpan; jika tipe berujung, field target/definisi selesai wajib terisi.
- **Business Rules:**
  - Goal tipe **berujung** wajib memiliki kriteria penyelesaian eksplisit (dinyatakan sebagai teks bebas oleh user, bukan dihitung otomatis oleh sistem, sesuai catatan Tahap 1: "definisi selesai berbeda per jenis aktivitas").
  - Goal tipe **berkelanjutan** tidak memiliki status `done` — progresnya diukur dari konsistensi Project/Habit terkait dari waktu ke waktu, bukan dari penyelesaian final.
- **Validation Rules:** Nama Goal wajib diisi; tipe (berujung/berkelanjutan) wajib dipilih saat pembuatan dan **tidak dapat diubah setelahnya** (mengubah tipe akan mengubah makna seluruh progres yang sudah tercatat — dilarang untuk menjaga integritas data historis).
- **Edge Cases:** Goal tanpa Project sama sekali → progresnya kosong/tidak terdefinisi, ditampilkan sebagai "belum ada aktivitas" di Dashboard, bukan error.
- **Exception Handling:** Tidak ada exception khusus di luar validasi standar.
- **State Machine (Goal berujung):** `active → completed` atau `active → abandoned` (dibatalkan sebagai tujuan, bukan tercapai).
- **Status Lifecycle (Goal berkelanjutan):** Hanya `active ↔ paused` — tidak ada status `completed` karena sifatnya tidak final.
- **Notification Behavior:** *(Future Enhancement)* notifikasi saat Goal tidak ada progres dalam waktu lama.
- **Reminder Behavior:** Tidak langsung — reminder terjadi di level Task/Project di bawahnya.
- **Permission Rules:** (lihat level modul — kepemilikan user).
- **Search Behavior:** Nama dan deskripsi Goal ter-index pencarian global.
- **Filtering:** Berdasarkan tipe (berujung/berkelanjutan) dan status.
- **Sorting:** Default berdasarkan `updated_at` terbaru atau berdasarkan progres terendah (untuk menyoroti Goal yang butuh perhatian).
- **Activity Log:** Mencatat perubahan status Goal dan riwayat Project yang pernah ditautkan.
- **Audit Trail:** Riwayat status disimpan permanen untuk mendukung Review jangka panjang (Blueprint bagian 14).
- **Future Enhancement:** Visualisasi tren progres jangka panjang, notifikasi idle Goal.

## 3.2 Fitur: Project Management

- **Functional Requirements:** User dapat membuat Project, opsional menautkannya ke satu Goal, dan mengelola Task di bawahnya.
- **Business Objectives:** Menjadi unit kerja menengah yang menerjemahkan Goal menjadi langkah konkret.
- **Workflow:** User membuat Project → (opsional) pilih Goal induk → menambahkan Task ke dalamnya → progres Project dihitung otomatis dari rasio Task `done` terhadap total Task aktif (tidak termasuk `archived`).
- **User Flow:** Lihat Blueprint bagian 6.
- **Use Case:** Bagian dari "Membuat & memecah Goal" serta digunakan luas di seluruh use case terkait Task.
- **Trigger:** Aksi user "Buat Project baru".
- **Preconditions:** User login; jika `goal_id` diisi, Goal tersebut harus milik user yang sama.
- **Postconditions:** Project baru tersimpan berstatus `active` dengan progres awal 0%.
- **Business Rules:** Progres Project **dihitung otomatis** (bukan diisi manual oleh user) dari proporsi Task berstatus `done` — ini penting untuk menjaga progres tetap jujur dan tidak bias (Blueprint bagian 3.5: "progres yang terlihat menjaga motivasi").
- **Validation Rules:** Nama Project wajib diisi.
- **Edge Cases:** Project tanpa Task sama sekali → progres ditampilkan sebagai "belum dimulai" (bukan 0% yang bisa disalahartikan sebagai "gagal").
- **Exception Handling:** Jika Task terakhir di sebuah Project di-archive/dihapus, progres dihitung ulang dari sisa Task yang ada; jika tidak ada Task tersisa sama sekali, kembali ke status "belum dimulai".
- **State Machine:** `active → completed` (seluruh Task relevan `done`, ditandai manual oleh user sebagai konfirmasi akhir — bukan otomatis 100% langsung menutup Project, karena user mungkin ingin menambah Task lagi) atau `active → archived` (dibatalkan).
- **Status Lifecycle:** `active → completed/archived`, tidak ada jalur balik otomatis (jika ingin dibuka lagi, user mengubah status manual kembali ke `active`).
- **Notification Behavior:** Tidak langsung.
- **Reminder Behavior:** Tidak langsung — melalui Task di dalamnya.
- **Permission Rules:** (lihat level modul).
- **Search Behavior:** Nama dan deskripsi Project ter-index.
- **Filtering:** Berdasarkan status, Goal induk, tag.
- **Sorting:** Default: progres terbaru diperbarui atau deadline terdekat (jika Project punya target waktu).
- **Activity Log:** Perubahan status dan riwayat progres tercatat dengan timestamp.
- **Audit Trail:** Riwayat lengkap perubahan status Project disimpan permanen untuk Review.
- **Future Enhancement:** Template Project (untuk alur berulang seperti "produksi video YouTube"), estimasi timeline otomatis berdasarkan riwayat Project serupa.

---

# MODUL 4 — TAGGING / CONTEXT

**Tujuan Modul:** Menjadi lapisan metadata lintas modul yang menghubungkan Task, Project, Note, dan Habit berdasarkan konteks yang sama.
**Permasalahan yang Diselesaikan:** Informasi tersebar tanpa cara menghubungkan aktivitas lintas jenis (mis. semua hal terkait "YouTube" tersebar di Task, Project, dan Note terpisah).
**Actor:** User.
**Dependencies:** Tidak bergantung ke modul lain — ini adalah fondasi (Must Have, hasil audit Blueprint #4) yang dikonsumsi oleh modul lain.
**Relationship dengan modul lain:** Dikonsumsi oleh Task, Project, Note, dan Habit sebagai relasi polymorphic; digunakan oleh Modul Search untuk filtering lintas modul.

## 4.1 Fitur: Tag Management & Assignment

- **Functional Requirements:** User dapat membuat Tag baru dan melekatkannya ke Task/Project/Note/Habit; satu entitas dapat memiliki lebih dari satu Tag.
- **Business Objectives:** Memungkinkan pandangan lintas modul berdasarkan konteks (mis. "semua yang berkaitan dengan Belajar Jepang").
- **Workflow:** User mengetik nama tag saat mengedit Task/Project/Note/Habit → jika tag belum ada, dibuat otomatis; jika sudah ada, ditautkan ke tag yang sama (case-insensitive, untuk mencegah duplikasi seperti "Youtube" dan "youtube").
- **User Flow:** Terintegrasi dalam alur pembuatan/edit entitas lain, tidak berdiri sendiri sebagai halaman utama.
- **Use Case:** Mendukung use case "Mencari lintas modul" dan seluruh use case pembuatan entitas lain.
- **Trigger:** User menambahkan tag saat mengedit entitas apa pun yang mendukung tagging.
- **Preconditions:** Entitas yang ditandai (Task/Project/Note/Habit) sudah ada dan dimiliki user yang login.
- **Postconditions:** Relasi polymorphic antara Tag dan entitas tersimpan.
- **Business Rules:** Nama tag bersifat unik per user (tidak ada dua tag dengan nama sama persis milik user yang sama, case-insensitive).
- **Validation Rules:** Nama tag 1–50 karakter, tanpa karakter khusus yang mengganggu URL/filter (mis. tanpa koma).
- **Edge Cases:** User menghapus Tag yang masih dipakai di banyak entitas → seluruh relasi ikut terputus (tag dihapus dari entitas terkait), entitas itu sendiri tidak terpengaruh selain kehilangan satu tag tersebut.
- **Exception Handling:** Percobaan membuat tag dengan nama yang sudah ada (case-insensitive) tidak menghasilkan duplikat — otomatis menautkan ke tag existing.
- **State Machine / Status Lifecycle:** Tidak berlaku (Tag tidak memiliki status, hanya ada/tidak ada).
- **Notification Behavior:** Tidak ada.
- **Reminder Behavior:** Tidak berlaku.
- **Permission Rules:** Tag hanya terlihat dan dapat digunakan oleh user pemiliknya sendiri.
- **Search Behavior:** Tag menjadi salah satu filter utama di Modul Search, memungkinkan pencarian lintas Task/Project/Note/Habit sekaligus.
- **Filtering:** Semua modul yang mendukung tagging dapat difilter berdasarkan satu atau kombinasi tag.
- **Sorting:** Tag diurutkan berdasarkan frekuensi pemakaian (tag paling sering dipakai muncul lebih dulu di autocomplete).
- **Activity Log:** Mencatat kapan tag dibuat dan terakhir dipakai.
- **Audit Trail:** Tidak memerlukan audit trail mendalam — risiko rendah.
- **Future Enhancement:** Pengelompokan tag (tag hierarkis/nested), penggabungan (merge) dua tag yang mirip menjadi satu.

---

# MODUL 5 — DASHBOARD / TODAY VIEW

**Tujuan Modul:** Menjawab pertanyaan "apa yang penting untuk saya kerjakan sekarang" dalam satu pandangan tunggal.
**Permasalahan yang Diselesaikan:** Bingung menentukan aktivitas paling penting saat ingin mulai bekerja.
**Actor:** User.
**Dependencies:** Tasks, Habit Tracking, Deadline Reminder.
**Relationship dengan modul lain:** Mengagregasi data dari Tasks, Habits, dan Notification — tidak menyimpan data sendiri (read-only aggregation layer).

## 5.1 Fitur: Today Aggregation View

- **Functional Requirements:** Menampilkan subset Task prioritas hari ini, checklist Habit hari ini, dan reminder aktif dalam satu halaman.
- **Business Objectives:** Mengurangi beban kognitif memilih pekerjaan; menjadi landing page default aplikasi.
- **Workflow:** Saat halaman dimuat → sistem mengambil Task dengan kriteria (prioritas tinggi ATAU deadline ≤ hari ini, status bukan `done`/`archived`) → mengambil Habit dengan jadwal hari ini → mengambil reminder yang belum dibaca → merender sebagai satu tampilan.
- **User Flow:** Buka aplikasi → mendarat di Dashboard (Blueprint bagian 6).
- **Use Case:** Mendukung hampir seluruh use case harian secara tidak langsung sebagai titik masuk.
- **Trigger:** User membuka aplikasi atau menavigasi ke halaman Today.
- **Preconditions:** User login.
- **Postconditions:** Tidak ada perubahan data — murni tampilan baca (read-only), kecuali user melakukan aksi (mis. tandai Task selesai) yang didelegasikan ke modul aslinya (Task/Habit).
- **Business Rules:** Dashboard **sengaja tidak menampilkan seluruh Task** — hanya subset yang relevan hari ini, untuk mencegah menjadi daftar tugas yang menumpuk kembali (Blueprint bagian 4.2 & 10).
- **Validation Rules:** Tidak berlaku (read-only).
- **Edge Cases:** Tidak ada Task/Habit relevan hari ini → tampilkan pesan kosong yang positif (mis. "tidak ada yang mendesak hari ini"), bukan tampilan error/kosong yang membingungkan.
- **Exception Handling:** Jika salah satu sumber data gagal dimuat (mis. query Habit error), bagian lain (Task, reminder) tetap ditampilkan — kegagalan parsial tidak boleh membuat seluruh Dashboard gagal tampil.
- **State Machine / Status Lifecycle:** Tidak berlaku.
- **Notification Behavior:** Menampilkan (bukan mengirim) notifikasi/reminder aktif yang relevan hari ini.
- **Reminder Behavior:** Menyajikan reminder yang sudah dijadwalkan oleh Modul Deadline Reminder/Full Notification Engine.
- **Permission Rules:** Data yang diagregasi selalu difilter berdasarkan kepemilikan user yang login.
- **Search Behavior:** Tidak memiliki search sendiri — mengarahkan ke Modul Search jika user butuh mencari di luar konteks hari ini.
- **Filtering:** *(Future Enhancement)* filter manual tambahan (mis. tampilkan berdasarkan Project tertentu saja).
- **Sorting:** Task diurutkan berdasarkan kombinasi prioritas dan kedekatan deadline.
- **Activity Log:** Tidak relevan (read-only aggregation).
- **Audit Trail:** Tidak berlaku.
- **Future Enhancement:** Kustomisasi komponen Dashboard oleh user (Blueprint bagian 4.1 — Could Have), snapshot progres Goal/Project ringkas di Dashboard.

---

# MODUL 6 — DEADLINE REMINDER

**Tujuan Modul:** Mengingatkan user secara proaktif terhadap Task/Project berdeadline, tanpa bergantung pada modul lain yang belum ada di tahap MVP.
**Permasalahan yang Diselesaikan:** Tidak ada pengingat aktif untuk deadline yang sudah direncanakan.
**Actor:** User (penerima), Sistem (pengirim otomatis via scheduled job).
**Dependencies:** Tasks, Projects (hanya — **tidak bergantung pada Habit**, sesuai perbaikan Catatan Audit Blueprint #1).
**Relationship dengan modul lain:** Membaca deadline dari Task/Project; hasilnya ditampilkan di Dashboard; preferensinya dibaca dari entitas `NotificationPreference` bersama (lihat Catatan Konsistensi di awal dokumen).

## 6.1 Fitur: Deadline Reminder Scheduling & Delivery

- **Functional Requirements:** Sistem secara otomatis membuat jadwal reminder saat Task/Project diberi deadline, dan mengirimkan reminder pada waktu yang telah ditentukan (mis. H-1 dan hari-H).
- **Business Objectives:** Mengurangi risiko deadline terlewat tanpa user perlu mengecek manual.
- **Workflow:** Task/Project disimpan dengan deadline → scheduled job harian memindai deadline yang jatuh dalam rentang reminder (H-1/H) → reminder dikirim (in-app, dan email/push sebagai Future Enhancement) → tercatat sebagai "reminder terkirim" agar tidak dikirim ulang.
- **User Flow:** Terintegrasi dengan alur Task/Project — user tidak perlu mengatur reminder secara manual (dibuat otomatis berdasarkan deadline).
- **Use Case:** "Menerima reminder" (Blueprint bagian 7).
- **Trigger:** Scheduled job berjalan harian (mis. setiap pagi).
- **Preconditions:** Task/Project berstatus aktif (bukan `done`/`archived`/`completed`) dan memiliki deadline yang jatuh dalam rentang reminder.
- **Postconditions:** Entri reminder tercatat sebagai terkirim; muncul di Dashboard sebagai reminder aktif.
- **Business Rules:** Reminder **tidak dikirim** untuk Task/Project yang sudah selesai/diarsipkan sebelum job berjalan — mencegah reminder yang tidak relevan lagi.
- **Validation Rules:** Tidak ada input manual dari user pada versi dasar (dijadwalkan otomatis dari deadline).
- **Edge Cases:** Deadline diubah setelah reminder H-1 terkirim → sistem menjadwalkan ulang reminder berdasarkan deadline baru dan menandai reminder lama sebagai kedaluwarsa.
- **Exception Handling:** Kegagalan pengiriman reminder (mis. job gagal) di-retry otomatis oleh mekanisme queue (Blueprint bagian 3, poin 16); jika gagal permanen, dicatat ke log untuk ditinjau saat maintenance, tidak gagal senyap.
- **State Machine / Status Lifecycle:** `scheduled → sent` atau `scheduled → cancelled` (jika Task/Project selesai sebelum waktunya).
- **Notification Behavior:** Reminder muncul di Dashboard dan (Future Enhancement) dikirim via email/push eksternal (Blueprint bagian 17, poin 4).
- **Reminder Behavior:** Reminder default: H-1 dan hari-H untuk deadline; dapat dinonaktifkan per Task melalui `NotificationPreference` (Future Enhancement untuk granularitas per-Task, MVP cukup on/off global).
- **Permission Rules:** Reminder hanya terlihat oleh pemilik Task/Project terkait.
- **Search Behavior:** Tidak berlaku langsung — reminder bukan entitas yang dicari, melainkan turunan dari Task/Project.
- **Filtering:** Dashboard dapat memfilter reminder yang sudah dibaca/belum.
- **Sorting:** Berdasarkan urgensi (deadline terdekat lebih dulu).
- **Activity Log:** Mencatat kapan setiap reminder dijadwalkan dan dikirim.
- **Audit Trail:** Riwayat reminder yang terkirim disimpan untuk keperluan debugging (memastikan tidak ada reminder yang terlewat/gagal).
- **Future Enhancement:** Kanal pengiriman eksternal (email, WhatsApp/Telegram bot — Blueprint bagian 17), granularitas waktu reminder per Task (bukan hanya H-1/H).

---

# MODUL 7 — HABIT TRACKING

**Tujuan Modul:** Melacak kebiasaan berulang dan konsistensinya dari waktu ke waktu.
**Permasalahan yang Diselesaikan:** Kesulitan membangun kebiasaan yang lebih konsisten dan disiplin.
**Actor:** User.
**Dependencies:** Auth/User, Tagging/Context.
**Relationship dengan modul lain:** Muncul di Dashboard sebagai checklist harian; menjadi sumber data Full Notification Engine (jadwal Habit) dan Review (data konsistensi).

## 7.1 Fitur: Habit Definition

- **Functional Requirements:** User dapat mendefinisikan Habit dengan nama, frekuensi target (harian/beberapa hari per minggu/mingguan), dan opsional tag.
- **Business Objectives:** Memberi struktur eksplisit pada kebiasaan yang ingin dibangun, bukan sekadar niat tanpa mekanisme pelacakan.
- **Workflow:** User membuat Habit baru → menentukan frekuensi → Habit muncul otomatis di Dashboard sesuai jadwalnya.
- **User Flow:** Dibuat sekali, lalu muncul otomatis setiap periode relevan (Blueprint bagian 9, Workflow Habit).
- **Use Case:** Bagian dari "Melacak Habit harian".
- **Trigger:** Aksi user "Buat Habit baru".
- **Preconditions:** User login.
- **Postconditions:** Habit baru tersimpan berstatus `active`.
- **Business Rules:** Frekuensi harus dipilih dari opsi terbatas (harian, N hari/minggu, mingguan) — bukan input bebas, untuk menjaga konsistensi perhitungan streak.
- **Validation Rules:** Nama wajib diisi; jika frekuensi "N hari/minggu", nilai N harus antara 1–7.
- **Edge Cases:** User mengubah frekuensi Habit yang sudah berjalan lama → perhitungan streak sebelumnya **tidak dihitung ulang secara retroaktif** (streak lama tetap dicatat apa adanya, streak baru dihitung berdasarkan aturan baru mulai dari titik perubahan) — mencegah distorsi data historis.
- **Exception Handling:** Tidak ada exception khusus di luar validasi standar.
- **State Machine / Status Lifecycle:** `active ↔ paused` (dijeda sementara tanpa dihapus) → `archived` (dihentikan permanen, riwayat tetap tersimpan untuk Review).
- **Notification Behavior:** Tidak langsung dari fitur ini.
- **Reminder Behavior:** Menjadi sumber jadwal untuk Full Notification Engine (Modul 11).
- **Permission Rules:** (lihat modul-modul lain — kepemilikan user).
- **Search Behavior:** Nama Habit ter-index pencarian global.
- **Filtering:** Berdasarkan status dan tag.
- **Sorting:** Berdasarkan urutan dibuat atau tingkat konsistensi (streak tertinggi/terendah).
- **Activity Log:** Mencatat perubahan frekuensi dan status.
- **Audit Trail:** Riwayat perubahan definisi Habit disimpan agar histori streak dapat ditelusuri konteksnya.
- **Future Enhancement:** Habit dengan target kuantitatif (mis. "baca 10 halaman", bukan sekadar centang ya/tidak).

## 7.2 Fitur: Habit Check-in & Streak Tracking

- **Functional Requirements:** User dapat mencentang (`check-in`) Habit pada hari berjalan; sistem menghitung streak konsistensi otomatis.
- **Business Objectives:** Memberi umpan balik visual langsung atas konsistensi, menjaga motivasi (Blueprint bagian 3.5).
- **Workflow:** Habit muncul di Dashboard hari ini → user mencentang → `HabitLog` tercatat untuk tanggal tersebut → streak dihitung ulang.
- **User Flow:** Bagian dari alur harian di Dashboard (Blueprint bagian 6).
- **Use Case:** "Melacak Habit harian" (Blueprint bagian 7).
- **Trigger:** User mencentang Habit di Dashboard.
- **Preconditions:** Habit berstatus `active` dan belum di-check-in untuk tanggal yang sama.
- **Postconditions:** `HabitLog` baru tersimpan untuk tanggal tersebut; streak diperbarui.
- **Business Rules:** Satu Habit hanya dapat memiliki satu `HabitLog` per tanggal (tidak bisa dicentang dua kali di hari yang sama untuk Habit harian).
- **Validation Rules:** Check-in hanya berlaku untuk tanggal hari ini atau retroaktif dalam batas wajar (mis. maksimal 2 hari ke belakang, untuk mengakomodasi lupa mencatat tanpa membuka celah manipulasi streak berlebihan).
- **Edge Cases:** User melewatkan satu hari pada Habit harian → streak reset ke 0 pada hari berikutnya sesuai aturan yang berlaku, kecuali frekuensi Habit adalah "N hari/minggu" dan target minggu tersebut masih dapat tercapai dari hari-hari tersisa (streak dihitung per periode, bukan hanya harian mentah).
- **Exception Handling:** Percobaan check-in ganda pada tanggal yang sama ditolak dengan pesan informatif, bukan error teknis mentah.
- **State Machine / Status Lifecycle:** `HabitLog` tidak memiliki status bertingkat — hanya ada/tidak ada per tanggal (`checked` atau tidak tercatat).
- **Notification Behavior:** Tidak langsung dari check-in itu sendiri.
- **Reminder Behavior:** Full Notification Engine mengingatkan jika Habit belum dicentang mendekati akhir hari (Modul 11).
- **Permission Rules:** (lihat Fitur 7.1).
- **Search Behavior:** Tidak berlaku langsung untuk `HabitLog` individual.
- **Filtering:** Riwayat `HabitLog` dapat difilter berdasarkan rentang tanggal untuk Review.
- **Sorting:** Berdasarkan tanggal, terbaru di atas.
- **Activity Log:** Setiap check-in tercatat dengan timestamp.
- **Audit Trail:** Riwayat lengkap `HabitLog` disimpan permanen sebagai dasar perhitungan tren konsistensi jangka panjang (Blueprint bagian 14).
- **Future Enhancement:** Visualisasi grid konsistensi (heatmap ala kalender kontribusi), pengingat berbasis pola (mis. mendeteksi waktu paling konsisten user melakukan Habit tertentu).

---

# MODUL 8 — KNOWLEDGE BASE / NOTES

**Tujuan Modul:** Menjadi tempat penyimpanan referensi, catatan, dan materi pembelajaran.
**Permasalahan yang Diselesaikan:** Catatan dan referensi tersebar di berbagai aplikasi.
**Actor:** User.
**Dependencies:** Auth/User, Tagging/Context.
**Relationship dengan modul lain:** Dapat ditautkan opsional ke Project; menjadi sumber data Modul Search.

## 8.1 Fitur: Note Creation & Management

- **Functional Requirements:** User dapat membuat, mengedit, dan menghapus Note berisi teks bebas (format ringan, mis. markdown dasar), dengan tag opsional.
- **Business Objectives:** Menjadi arsip pengetahuan pribadi yang dapat dicari kembali kapan pun, mengurangi fragmentasi informasi.
- **Workflow:** User membuat Note baru (langsung atau dari hasil triase Inbox) → mengisi konten → simpan.
- **User Flow:** Dapat diakses langsung dari menu Knowledge Base, atau hasil konversi dari Inbox Triage (Blueprint bagian 6).
- **Use Case:** "Menyimpan referensi belajar" (Blueprint bagian 7).
- **Trigger:** Aksi user "Buat Note baru" atau hasil triase Inbox.
- **Preconditions:** User login.
- **Postconditions:** Note baru tersimpan.
- **Business Rules:** Note tidak memiliki status penyelesaian (bukan Task) — sifatnya murni arsip, tidak ada konsep "selesai/belum selesai" untuk sebuah catatan.
- **Validation Rules:** Judul Note wajib diisi; konten boleh kosong pada saat pembuatan awal (mis. hanya menyimpan judul dulu, isi menyusul).
- **Edge Cases:** Note sangat panjang (mis. materi belajar detail) → tidak dibatasi ketat, namun disarankan pengelompokan dengan Note terpisah bertautan tag yang sama untuk keterbacaan.
- **Exception Handling:** Tidak ada exception khusus di luar validasi standar.
- **State Machine / Status Lifecycle:** Tidak bertingkat — Note hanya ada dua kondisi praktis: `active` atau `archived` (diarsipkan agar tidak muncul di daftar aktif, namun tetap dapat dicari).
- **Notification Behavior:** Tidak ada.
- **Reminder Behavior:** Tidak berlaku.
- **Permission Rules:** Hanya terlihat oleh pemilik.
- **Search Behavior:** Judul dan isi Note ter-index penuh untuk pencarian global — Knowledge Base adalah salah satu sumber data terbesar bagi Modul Search.
- **Filtering:** Berdasarkan tag, status (`active`/`archived`), dan Project terkait (jika ditautkan).
- **Sorting:** Default: terakhir diperbarui (`updated_at DESC`).
- **Activity Log:** Mencatat `created_at`/`updated_at`.
- **Audit Trail:** Riwayat versi konten *(Future Enhancement)* — MVP hanya menyimpan versi terakhir.
- **Future Enhancement:** Bidirectional linking antar-Note (ala Zettelkasten), version history, embed gambar/lampiran (Blueprint bagian 15, Storage Strategy).

## 8.2 Fitur: Note Linking to Projects

- **Functional Requirements:** User dapat menautkan satu Note ke satu Project (opsional).
- **Business Objectives:** Menghubungkan referensi/riset dengan pekerjaan produksi yang relevan (mis. riset untuk video YouTube tertentu).
- **Workflow:** Saat mengedit Note, user memilih Project tujuan dari daftar Project miliknya.
- **User Flow:** Terintegrasi dalam halaman detail Project (menampilkan Note tertaut) dan halaman edit Note.
- **Use Case:** Pendukung "Menyimpan referensi belajar" dan tampilan detail Project (Blueprint bagian 4.2 — Project Detail wireframe).
- **Trigger:** User memilih/mengubah Project tertaut pada sebuah Note.
- **Preconditions:** Project yang dipilih harus milik user yang sama.
- **Postconditions:** Relasi `note.project_id` tersimpan (nullable).
- **Business Rules:** Satu Note hanya dapat ditautkan ke **satu** Project (bukan banyak) — untuk menjaga kesederhanaan model data; jika Note relevan untuk banyak Project, gunakan Tag alih-alih relasi langsung.
- **Validation Rules:** Jika `project_id` diisi, harus merujuk Project yang valid dan aktif.
- **Edge Cases:** Project yang ditautkan dihapus/diarsipkan → `project_id` pada Note otomatis di-set null (Note tidak ikut terhapus), Note kembali menjadi Note independen.
- **Exception Handling:** Race condition saat Project dihapus bersamaan Note sedang ditautkan → transaksi database memastikan salah satu operasi selesai lebih dulu secara konsisten, tidak ada state Note menautkan ke Project yang sudah tidak ada (foreign key constraint dengan `on delete set null`).
- **State Machine / Status Lifecycle:** Tidak berlaku terpisah dari status Note itu sendiri.
- **Notification Behavior:** Tidak ada.
- **Reminder Behavior:** Tidak berlaku.
- **Permission Rules:** (lihat Fitur 8.1).
- **Search Behavior:** Note yang tertaut ke Project dapat difilter/ditemukan lewat konteks Project tersebut.
- **Filtering:** Halaman detail Project menampilkan seluruh Note tertaut sebagai daftar terpisah dari Task.
- **Sorting:** Berdasarkan `updated_at` Note dalam konteks Project.
- **Activity Log:** Mencatat perubahan `project_id` pada Note.
- **Audit Trail:** Tidak memerlukan detail tambahan di luar log perubahan biasa.
- **Future Enhancement:** Menautkan satu Note ke banyak Project (many-to-many) jika kebutuhan nyata muncul di kemudian hari.

---

# MODUL 9 — FOCUS MODE

**Tujuan Modul:** Menyediakan ruang kerja bebas distraksi untuk mengeksekusi satu Task.
**Permasalahan yang Diselesaikan:** Sering berpindah dari satu pekerjaan ke pekerjaan lain tanpa menyelesaikan pekerjaan sebelumnya.
**Actor:** User.
**Dependencies:** Tasks (satu-satunya sumber data — Focus Mode tidak memiliki entitas sendiri, sesuai klarifikasi Blueprint bagian 8).
**Relationship dengan modul lain:** Menarik satu Task aktif sebagai state UI sementara; menandai Task selesai melalui aksi yang sama dengan Modul Tasks (bukan mekanisme terpisah).

## 9.1 Fitur: Focus Session

- **Functional Requirements:** User dapat memilih satu Task untuk masuk ke tampilan minim-distraksi, dengan opsi timer sesi kerja (mis. teknik Pomodoro sebagai opsi, bukan wajib).
- **Business Objectives:** Membantu user tetap berada dalam satu pekerjaan hingga selesai atau hingga sesi berakhir dengan sengaja.
- **Workflow:** User memilih "Fokus" pada sebuah Task (dari Dashboard/Project) → tampilan menyempit menjadi satu Task besar + tombol selesai + timer opsional → user menyelesaikan Task atau mengakhiri sesi.
- **User Flow:** Dashboard/Focus Mode → satu Task dipilih → dikerjakan → selesai/keluar (Blueprint bagian 6).
- **Use Case:** "Masuk Focus Mode" (Blueprint bagian 7).
- **Trigger:** Aksi user "Mulai Fokus" pada sebuah Task.
- **Preconditions:** Task yang dipilih berstatus `todo` atau `in_progress` dan dimiliki user yang login.
- **Postconditions:** Tidak ada perubahan data permanen kecuali user secara eksplisit menandai Task selesai/mengubah status di dalam sesi (didelegasikan ke Modul Tasks) — Focus Mode sendiri tidak menyimpan entitas "sesi" ke database pada versi MVP (sesi bersifat state front-end sementara).
- **Business Rules:** Saat Focus Mode aktif, hanya satu Task yang ditampilkan; navigasi ke modul lain disembunyikan sementara namun tetap dapat diakses via tombol keluar eksplisit (tidak mengunci user secara paksa).
- **Validation Rules:** Tidak berlaku (tidak ada input data baru selain melalui aksi Task yang sudah ada).
- **Edge Cases:** User menutup browser/refresh saat sesi Focus Mode aktif → sesi hilang (karena bersifat state sementara di MVP), namun status Task itu sendiri (jika sempat diubah) tetap tersimpan seperti biasa.
- **Exception Handling:** Tidak ada exception khusus — seluruh operasi data didelegasikan ke Modul Tasks yang sudah punya penanganannya sendiri.
- **State Machine / Status Lifecycle:** Tidak berlaku (Focus Mode bukan entitas berstatus, murni state tampilan: `inactive → active → inactive`).
- **Notification Behavior:** Notifikasi lain *(Future Enhancement)* dapat disembunyikan sementara selama Focus Mode aktif, untuk menjaga prinsip "calm technology".
- **Reminder Behavior:** Reminder yang jatuh tempo selama sesi Focus Mode tetap muncul di Dashboard setelah sesi berakhir, tidak hilang.
- **Permission Rules:** Mengikuti permission Task yang sedang difokuskan.
- **Search Behavior:** Tidak berlaku.
- **Filtering/Sorting:** Tidak berlaku (hanya satu Task pada satu waktu).
- **Activity Log:** *(Future Enhancement)* mencatat durasi sesi Focus Mode sebagai data untuk Review (mis. "total waktu fokus minggu ini") — belum termasuk MVP karena Focus Mode belum memiliki entitas data sendiri.
- **Audit Trail:** Tidak berlaku pada versi MVP.
- **Future Enhancement:** Entitas `FocusSession` tersendiri (durasi, waktu mulai/selesai) untuk mendukung statistik waktu fokus di Review; integrasi timer Pomodoro dengan notifikasi jeda otomatis.

---

# MODUL 10 — REVIEW & REFLECTION

**Tujuan Modul:** Menjadi ritual peninjauan progres berkala yang menutup siklus Capture–Organize–Prioritize–Execute.
**Permasalahan yang Diselesaikan:** Progres tujuan jangka panjang tidak terlihat sehingga motivasi menurun; tidak ada ritual refleksi terjadwal.
**Actor:** User (peninjau), Sistem (agregator data & pengirim reminder ritual).
**Dependencies:** Tasks, Projects & Goals, Habit Tracking (sebagai sumber data agregat).
**Relationship dengan modul lain:** Membaca data historis dari seluruh modul lain; memicu reminder dari Full Notification Engine (jadwal ritual).

## 10.1 Fitur: Daily Review

- **Functional Requirements:** Menyajikan ringkasan Task selesai hari ini dan Habit yang tercentang, dengan ruang catatan refleksi bebas.
- **Business Objectives:** Menutup hari dengan kesadaran progres, mendukung penyesuaian rencana esok hari.
- **Workflow:** User membuka Review > Daily (manual atau via reminder) → sistem menampilkan ringkasan hari itu → user menulis catatan refleksi singkat → `ReviewEntry` (tipe `daily`) tersimpan.
- **User Flow:** Bagian akhir dari alur harian (Blueprint bagian 8, Workflow Dashboard & Review).
- **Use Case:** "Melakukan Review berkala" (Blueprint bagian 7).
- **Trigger:** Aksi manual user atau reminder ritual dari Full Notification Engine.
- **Preconditions:** User login.
- **Postconditions:** `ReviewEntry` baru tersimpan dengan snapshot metrik ringkas (jumlah Task selesai, Habit tercentang) dan catatan refleksi teks.
- **Business Rules:** Satu `ReviewEntry` tipe `daily` per tanggal per user (tidak dobel).
- **Validation Rules:** Catatan refleksi bersifat opsional (boleh kosong — user tetap dapat menyimpan Review hanya dengan snapshot metrik tanpa menulis apa pun, agar ritual tidak terasa memberatkan).
- **Edge Cases:** User membuka Daily Review di hari yang sama lebih dari sekali → entri yang sama diperbarui (edit), bukan membuat duplikat.
- **Exception Handling:** Kegagalan mengambil salah satu sumber data (mis. Habit) tidak menggagalkan keseluruhan Review — bagian yang gagal ditampilkan sebagai "data tidak tersedia" sementara bagian lain tetap tampil.
- **State Machine / Status Lifecycle:** Tidak bertingkat — `ReviewEntry` hanya ada/tidak ada per periode, dapat diedit kapan saja setelah dibuat.
- **Notification Behavior:** Tidak ada notifikasi keluar dari fitur ini sendiri.
- **Reminder Behavior:** Dipicu oleh Full Notification Engine sebagai reminder ritual (Modul 11).
- **Permission Rules:** Hanya terlihat oleh pemilik.
- **Search Behavior:** Catatan refleksi ter-index untuk pencarian (berguna menelusuri kembali pemikiran masa lalu).
- **Filtering:** Berdasarkan rentang tanggal.
- **Sorting:** Terbaru di atas.
- **Activity Log:** Mencatat kapan Review dibuat/diedit.
- **Audit Trail:** Riwayat penuh `ReviewEntry` disimpan permanen sebagai jejak reflektif jangka panjang.
- **Future Enhancement:** Ringkasan otomatis berbasis AI (Blueprint bagian 16, poin 4).

## 10.2 Fitur: Weekly & Monthly Review

- **Functional Requirements:** Menyajikan agregasi progres Goal/Project dan tren konsistensi Habit selama periode mingguan/bulanan.
- **Business Objectives:** Menjaga keselarasan aktivitas harian dengan arah jangka panjang (Goal).
- **Workflow:** User membuka Review > Weekly/Monthly (manual atau via reminder ritual) → sistem mengagregasi data dari periode terkait → user menulis refleksi dan menyesuaikan prioritas periode berikutnya.
- **User Flow:** Blueprint bagian 6 (alur "Review mingguan").
- **Use Case:** "Melakukan Review berkala" (Blueprint bagian 7).
- **Trigger:** Aksi manual atau reminder ritual mingguan/bulanan.
- **Preconditions:** User login.
- **Postconditions:** `ReviewEntry` (tipe `weekly`/`monthly`) tersimpan dengan snapshot agregat periode tersebut.
- **Business Rules:** Snapshot metrik periode **dibekukan** pada saat Review dibuat (tidak berubah otomatis jika data sumber berubah belakangan) — ini penting agar Review menjadi catatan historis yang akurat mencerminkan kondisi saat itu, bukan angka yang terus bergeser.
- **Validation Rules:** Sama seperti Daily Review — catatan refleksi opsional.
- **Edge Cases:** Periode yang di-review belum memiliki data sama sekali (mis. user baru mulai memakai aplikasi) → ditampilkan sebagai "belum ada aktivitas tercatat", bukan error.
- **Exception Handling:** Sama seperti Daily Review — kegagalan parsial tidak menggagalkan keseluruhan.
- **State Machine / Status Lifecycle:** Sama seperti Daily Review, dibedakan berdasarkan `period_type` (`weekly`/`monthly`).
- **Notification Behavior:** Tidak langsung.
- **Reminder Behavior:** Dipicu oleh Full Notification Engine (Modul 11) sesuai jadwal ritual yang dapat diatur user.
- **Permission Rules:** (lihat Fitur 10.1).
- **Search Behavior:** (lihat Fitur 10.1).
- **Filtering:** Berdasarkan periode dan tipe (`weekly`/`monthly`).
- **Sorting:** Terbaru di atas.
- **Activity Log:** (lihat Fitur 10.1).
- **Audit Trail:** Snapshot metrik yang dibekukan menjadi bagian penting audit trail jangka panjang untuk melihat tren nyata dari waktu ke waktu.
- **Future Enhancement:** Grafik tren progres Goal lintas beberapa periode Review sekaligus (Blueprint bagian 14, Tahun 2).

---

# MODUL 11 — FULL NOTIFICATION ENGINE

**Tujuan Modul:** Melengkapi Deadline Reminder dengan reminder berbasis jadwal Habit dan ritual Review — dibangun setelah kedua sumber data tersebut tersedia (v0.6, sesuai Blueprint bagian 12.2).
**Permasalahan yang Diselesaikan:** Kebutuhan pengingat aktif yang menyeluruh, tidak hanya deadline.
**Actor:** User (penerima), Sistem (pengirim otomatis).
**Dependencies:** Deadline Reminder (Modul 6, sebagai lapisan dasar yang sudah ada), Habit Tracking (Modul 7), Review & Reflection (Modul 10).
**Relationship dengan modul lain:** Memperluas cakupan reminder dari Modul 6 tanpa menggantikannya; membaca `NotificationPreference` yang sama (lihat Catatan Konsistensi di awal dokumen).

## 11.1 Fitur: Habit Schedule Notifications

- **Functional Requirements:** Mengingatkan user jika Habit terjadwal hari ini belum dicentang, mendekati akhir hari.
- **Business Objectives:** Menjaga konsistensi Habit tanpa mengandalkan user mengingat sendiri.
- **Workflow:** Scheduled job berjalan pada waktu tertentu (mis. sore/malam) → memeriksa Habit `active` yang jadwalnya jatuh hari ini dan belum ada `HabitLog` untuk tanggal tersebut → mengirim reminder.
- **User Flow:** Notifikasi muncul di Dashboard/kanal eksternal, mengarahkan user untuk check-in.
- **Use Case:** "Menerima reminder" (Blueprint bagian 7), diperluas untuk konteks Habit.
- **Trigger:** Scheduled job harian pada waktu yang dapat dikonfigurasi user.
- **Preconditions:** Habit berstatus `active`, jadwal jatuh hari ini, belum ada `HabitLog` untuk tanggal berjalan.
- **Postconditions:** Reminder tercatat sebagai terkirim; tidak dikirim ulang di hari yang sama.
- **Business Rules:** Reminder tidak dikirim jika Habit sudah dicentang sebelum waktu pengiriman (mengecek ulang kondisi tepat sebelum kirim, bukan hanya di awal hari).
- **Validation Rules:** Waktu pengiriman reminder dapat diatur user melalui `NotificationPreference` (mis. jam 20:00), dengan default jika belum diatur.
- **Edge Cases:** User mengubah waktu pengiriman di tengah hari setelah job pertama sudah berjalan → perubahan berlaku mulai hari berikutnya, tidak retroaktif untuk hari yang sedang berjalan.
- **Exception Handling:** Sama seperti Modul 6 — retry otomatis via queue, dicatat ke log jika gagal permanen.
- **State Machine / Status Lifecycle:** `scheduled → sent` atau `scheduled → skipped` (jika Habit sudah dicentang sebelum waktu kirim).
- **Notification Behavior:** In-app (MVP) dan kanal eksternal (Future Enhancement, Blueprint bagian 17).
- **Reminder Behavior:** Satu reminder per Habit per hari maksimum — tidak berulang-ulang mengganggu (prinsip Calm Technology).
- **Permission Rules:** Hanya untuk pemilik Habit.
- **Search Behavior:** Tidak berlaku.
- **Filtering:** Dashboard dapat memfilter reminder berdasarkan sumber (Deadline vs Habit vs Review Ritual).
- **Sorting:** Berdasarkan waktu kirim.
- **Activity Log:** Mencatat setiap reminder yang dijadwalkan/dikirim/dilewati.
- **Audit Trail:** Riwayat pengiriman disimpan untuk debugging dan analisis pola (mis. Habit mana yang paling sering butuh reminder — indikasi tidak lagi relevan).
- **Future Enhancement:** Reminder adaptif berdasarkan pola waktu user biasa check-in (personalisasi waktu kirim otomatis).

## 11.2 Fitur: Review Ritual Reminders

- **Functional Requirements:** Mengingatkan user untuk melakukan Daily/Weekly/Monthly Review sesuai jadwal yang ditentukan.
- **Business Objectives:** Memastikan ritual refleksi benar-benar dijalankan rutin, menjawab risiko dari Blueprint bagian 13: "progress tracking yang tidak pernah dilihat rutin".
- **Workflow:** Scheduled job memeriksa apakah `ReviewEntry` untuk periode berjalan (hari ini/minggu ini/bulan ini) sudah dibuat → jika belum dan sudah mendekati akhir periode, kirim reminder.
- **User Flow:** Notifikasi mengarahkan user langsung ke halaman Review terkait.
- **Use Case:** "Menerima reminder" (konteks ritual Review).
- **Trigger:** Scheduled job harian/mingguan/bulanan sesuai tipe Review.
- **Preconditions:** Belum ada `ReviewEntry` untuk periode yang bersangkutan.
- **Postconditions:** Reminder tercatat sebagai terkirim.
- **Business Rules:** Reminder Weekly dikirim mendekati akhir minggu (mis. Minggu sore); reminder Monthly mendekati akhir bulan — waktu default dapat disesuaikan user.
- **Validation Rules:** (lihat Fitur 11.1 — pola serupa untuk preferensi waktu).
- **Edge Cases:** User sudah mengisi Review lebih awal dari jadwal reminder → reminder untuk periode tersebut otomatis dibatalkan (tidak jadi dikirim).
- **Exception Handling:** (lihat Fitur 11.1).
- **State Machine / Status Lifecycle:** `scheduled → sent` atau `scheduled → cancelled` (Review sudah diisi lebih dulu).
- **Notification Behavior:** (lihat Fitur 11.1).
- **Reminder Behavior:** Maksimum satu reminder ritual per periode per tipe Review.
- **Permission Rules:** Hanya untuk pemilik data.
- **Search Behavior:** Tidak berlaku.
- **Filtering/Sorting:** (lihat Fitur 11.1).
- **Activity Log/Audit Trail:** (lihat Fitur 11.1).
- **Future Enhancement:** Reminder cerdas yang menyesuaikan jika user konsisten selalu mengisi Review lebih awal/lambat dari jadwal default.

---

# MODUL 12 — SEARCH (LINTAS MODUL)

**Tujuan Modul:** Menyediakan akses cepat ke seluruh entitas (Task, Project, Note, Habit) dari satu titik pencarian.
**Permasalahan yang Diselesaikan:** Kesulitan menemukan kembali informasi yang tersebar di berbagai modul.
**Actor:** User.
**Dependencies:** Tasks, Projects & Goals, Knowledge Base, Habit Tracking, Tagging/Context (seluruh modul sebagai sumber index).
**Relationship dengan modul lain:** Read-only consumer dari seluruh modul — tidak memiliki data sendiri.

## 12.1 Fitur: Global Search

- **Functional Requirements:** User dapat mencari teks bebas yang mencocokkan judul/isi Task, Project, Note, dan Habit sekaligus, dengan hasil terkategorikan per tipe.
- **Business Objectives:** Mengurangi waktu yang dibutuhkan untuk menemukan kembali informasi, mendukung prinsip "satu sumber kebenaran".
- **Workflow:** User mengetik query di search bar global → sistem mencocokkan ke index judul/isi seluruh entitas milik user → hasil ditampilkan dikelompokkan per tipe entitas.
- **User Flow:** Dapat diakses dari halaman mana pun via search bar global (Blueprint bagian 19).
- **Use Case:** "Mencari lintas modul" (Blueprint bagian 7).
- **Trigger:** User mengetik query pencarian.
- **Preconditions:** User login.
- **Postconditions:** Tidak ada perubahan data (read-only).
- **Business Rules:** Hasil pencarian **selalu difilter berdasarkan kepemilikan user** — tidak ada kebocoran data lintas user meskipun di masa depan aplikasi menjadi multi-user (Blueprint bagian 19).
- **Validation Rules:** Query minimum 2 karakter untuk menghindari hasil terlalu luas/tidak relevan.
- **Edge Cases:** Query tidak menghasilkan hasil sama sekali → tampilkan pesan "tidak ditemukan" beserta saran (mis. coba kata kunci lain atau gunakan filter tag).
- **Exception Handling:** Kegagalan pencarian pada satu sumber data (mis. index Note bermasalah) tidak menggagalkan hasil dari sumber lain — hasil parsial tetap ditampilkan.
- **State Machine / Status Lifecycle:** Tidak berlaku.
- **Notification Behavior:** Tidak ada.
- **Reminder Behavior:** Tidak berlaku.
- **Permission Rules:** (lihat Business Rules).
- **Search Behavior:** Ini adalah inti dari fitur ini — pencarian mencakup judul, isi teks, dan tag lintas Task/Project/Note/Habit.
- **Filtering:** Dapat difilter lebih lanjut berdasarkan tipe entitas dan tag setelah hasil awal muncul.
- **Sorting:** Relevansi (kecocokan teks) sebagai default, dengan opsi urutkan berdasarkan tanggal terbaru.
- **Activity Log:** *(Opsional/Future Enhancement)* mencatat query yang sering dicari untuk membantu autocomplete di masa depan.
- **Audit Trail:** Tidak diperlukan untuk fitur read-only ini.
- **Future Enhancement:** Pencarian berbasis waktu ("apa yang saya kerjakan minggu lalu" — Blueprint bagian 19), full-text search lanjutan memanfaatkan fitur PostgreSQL (Blueprint bagian 11), pencarian semantik berbasis AI.

---

## Penutup

Dokumen FSD ini konsisten sepenuhnya dengan Blueprint v1.0, dengan satu klarifikasi kecil terhadap kepemilikan `NotificationPreference` (lihat Catatan Konsistensi di awal dokumen). Setiap modul dan fitur di atas dapat langsung dijadikan acuan saat menulis migration, model, Action/Service class, dan Livewire component sesuai struktur folder yang telah ditetapkan di Blueprint bagian 9 (System Architecture) — tanpa perlu keputusan desain tambahan di tengah proses implementasi.
