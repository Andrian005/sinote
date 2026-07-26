# DECISIONS.md

> Log keputusan yang sudah **final** — baik yang berasal dari 6 dokumen acuan maupun yang muncul baru selama implementasi. AI wajib membaca file ini sebelum menulis kode (lihat `WORKFLOW.md`) agar tidak mengusulkan ulang keputusan yang sudah diputuskan.

## Format Entri

```
### D-{nomor} — {judul singkat}
- Tanggal: {tanggal}
- Konteks: {kenapa keputusan ini perlu diambil}
- Keputusan: {apa yang diputuskan}
- Alasan: {kenapa ini yang dipilih, bukan alternatif lain}
- Sumber: {dokumen acuan atau "diputuskan selama implementasi"}
- Status: Final / Ditinjau Ulang pada {tanggal}
```

---

## Keputusan dari Dokumen Acuan (Final, Referensi ADR Terkait)

### D-001 — Modular Monolith, Bukan Microservices
- Sumber: Blueprint v1.0 bagian 9, TDD bagian 2. Lihat `docs/decisions/adr/0001-modular-monolith.md`.
- Status: Final.

### D-002 — Tanpa Repository Pattern
- Sumber: TDD bagian 11. Lihat `docs/decisions/adr/0002-no-repository-pattern.md`.
- Status: Final.

### D-003 — PostgreSQL sebagai Database Final
- Sumber: Blueprint v1.0 (hasil audit), TDD bagian 25. Lihat `docs/decisions/adr/0003-database-postgresql.md`.
- Status: Final.

### D-004 — Tagging/Context Dinaikkan Menjadi Must Have
- Konteks: Blueprint Tahap 2 awalnya menandai Tagging/Context sebagai "Should Have" namun memperlakukannya sebagai fondasi wajib — inkonsistensi ditemukan saat audit Blueprint v1.0.
- Keputusan: Tagging/Context berstatus **Must Have**, dibangun sebagai bagian awal Core Infrastructure/Shared, sebelum modul fitur pertama.
- Sumber: Blueprint v1.0, Catatan Audit #4.
- Status: Final.

### D-005 — Reminder Dipecah Dua Lapis
- Konteks: Reminder awalnya direncanakan satu modul, namun Deadline Reminder (butuh Task/Project saja) dan Full Notification Engine (butuh Habit+Review) memiliki dependency data yang berbeda waktu ketersediaannya.
- Keputusan: Deadline Reminder dibangun di MVP (v0.2); Full Notification Engine dibangun belakangan (v0.6) setelah Habit dan Review tersedia. Keduanya berbagi satu tabel `reminders` (dibedakan `reminder_type`) dan satu `notification_preferences`.
- Sumber: Blueprint v1.0 Catatan Audit #1, FSD Modul 6 & 11, Database Spec A.11–A.12.
- Status: Final.

### D-006 — Focus Mode adalah Lapisan UI, Bukan Entitas Data
- Sumber: Blueprint v1.0 Catatan Audit #2, FSD Modul 9, TDD (klarifikasi ARCHITECTURE_RULES.md).
- Status: Final.

## Keputusan Baru Selama Implementasi

### D-008 — PestPHP sebagai Test Runner
- Tanggal: 2026-07-25
- Konteks: Template Laravel yang dipakai pada TASK-0001 hanya menyertakan PHPUnit, sedangkan TDD dan `LARAVEL_RULES.md` mewajibkan PestPHP.
- Keputusan: Tambahkan `pestphp/pest` sebagai dependency development dan gunakan sintaks Pest untuk test proyek.
- Alasan: Menjaga kepatuhan pada strategi testing proyek dengan sintaks yang lebih ringkas untuk solo developer.
- Sumber: diputuskan selama implementasi.
- Status: Final.

### D-007 — Predis sebagai Client Redis Lokal
- Tanggal: 2026-07-25
- Konteks: PHP 8.3 Laragon lokal tidak menyertakan extension `phpredis`, sementara TASK-0001 mewajibkan Redis aktif untuk cache, queue, dan session.
- Keputusan: Gunakan package `predis/predis` sebagai client Redis Laravel pada environment lokal.
- Alasan: Predis adalah client PHP yang matang dan didukung Laravel, sehingga Redis lokal dapat dipakai tanpa memasang DLL extension PHP manual yang rentan terhadap ketidakcocokan versi.
- Sumber: diputuskan selama implementasi.
- Status: Final.

### D-009 — FK `project_id` di Tabel `tasks` Tanpa Constraint Sementara
- Tanggal: 2026-07-26
- Konteks: TASK-0011 membuat tabel `tasks` yang memiliki kolom `project_id` (nullable), namun tabel `projects` belum ada — baru akan dibuat di EPIC-004.
- Keputusan: Kolom `project_id` dibuat sebagai plain ULID dengan `->index()` saja, tanpa FK constraint ke `projects.id`. Constraint akan ditambahkan via ALTER TABLE migration terpisah di EPIC-004.
- Alasan: Menghindari dependency tabel yang belum ada tanpa menciptakan tabel `projects` stub yang setengah jadi. Pendekatan ini lebih bersih daripada stub kosong karena tidak menimbulkan ambiguitas saat EPIC-004 mulai mengisi tabel tersebut.
- Implikasi: Selama EPIC-003 berjalan, `project_id` di Task bersifat informatif — tidak ada referential integrity di DB level, namun ownership divalidasi di Application layer (Action `CreateTask`).
- Status: Final, akan di-resolve di EPIC-004.

*(Kosong — diisi seiring proyek berjalan. Setiap entri baru mengikuti format di atas.)*
