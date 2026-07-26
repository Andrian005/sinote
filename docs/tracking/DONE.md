# DONE.md

> Riwayat tiket yang sudah selesai, terbaru di atas. Setiap entri wajib menyertakan tanggal selesai dan tautan ke file tiket (yang statusnya sudah diubah menjadi `Done`).

## Format Entri

```
### {ID} — {Judul}
- Selesai: {tanggal}
- File: {path ke file tiket}
- Catatan singkat: {ringkasan 1 kalimat, opsional}
```

---

---

### TASK-0005 — Migration `tags` + `taggables` & Model Tag
- Selesai: 2026-07-26
- File: `tickets/tasks/TASK-0005-migration-model-tag.md`
- Catatan singkat: Migration tags (posisi 2) dan taggables (posisi 9) sesuai Database Spec A.9, Model Tag dengan HasUlids + morphedByMany stubs, migrate:fresh dan 22 test hijau.

### FEAT-0001 — Kickoff EPIC-001 (Tagging/Context) — Pemecahan Menjadi TASK
- Selesai: 2026-07-26
- File: `tickets/features/FEAT-0001-kickoff-tagging-context.md`
- Catatan singkat: EPIC-001 dipecah menjadi TASK-0005 (migration + model), TASK-0006 (factory + policy + actions + unit test), TASK-0007 (Livewire + feature test + seeder); NEXT_TASK.md diperbarui; TASK-0005 menjadi tiket aktif.

### TASK-0004 — Base Policy Pattern & Contoh Penerapan
- Selesai: 2026-07-25
- File: `tickets/tasks/TASK-0004-base-policy-pattern.md`
- Catatan singkat: Pola Policy standar (Pola A user_id + Pola B self) terdokumentasi, ExamplePolicy sebagai template dibuat, 13 unit test hijau, route group auth direfactor, EPIC-000 selesai.

### TASK-0003 — Auth (Breeze/Fortify) & Halaman Login
- Selesai: 2026-07-25
- File: `tickets/tasks/TASK-0003-auth-setup.md`
- Catatan singkat: Breeze/Fortify terpasang, login/register/logout berfungsi, rate limiting 5/menit aktif, redirect ke /today, Sanctum siap.

### TASK-0002 — Migration & Model `users`
- Selesai: 2026-07-25
- File: `tickets/tasks/TASK-0002-migration-model-users.md`
- Catatan singkat: Tabel dan Model User ber-ULID, factory, serta test Pest telah dibuat.

### TASK-0001 — Setup Environment Development Lokal
- Selesai: 2026-07-25
- File: `tickets/tasks/TASK-0001-setup-environment.md`
- Catatan singkat: Laravel, PostgreSQL, Redis, Git, dan struktur domain dasar telah siap.

*(Belum ada tiket yang selesai — proyek baru dimulai dari Sprint 1)*

### TASK-0006 — Factory + Policy + Action Tag + Unit Test
- Selesai: 2026-07-26
- File: `tickets/tasks/TASK-0006-factory-policy-action-tag.md`
- Catatan singkat: TagFactory dengan state methods, TagPolicy terdaftar, StoreTagRequest memvalidasi name, CreateTag/AttachTag/DetachTag Actions dengan 20 unit tests hijau (14 Action + 6 Policy), 42 tests total passed.

### TASK-0007 — Livewire Tag Input Component + Feature Test + TagSeeder
- Selesai: 2026-07-26
- File: `tickets/tasks/TASK-0007-livewire-tag-input-seeder-feature-test.md`
- Catatan singkat: TagInput component dengan autocomplete, create-on-type, attach/detach; 10 feature tests mencakup filtering, user isolation, authorization; TagSeeder dengan 12 sample tags; 52 tests total passed.

### EPIC-001 — Tagging & Context (Lapisan Metadata Lintas Modul)
- Selesai: 2026-07-26
- File: `tickets/epics/EPIC-001-tagging-context.md`
- Catatan singkat: EPIC lengkap — 3 TASK (TASK-0005, TASK-0006, TASK-0007) dengan 32 tests (20 unit Actions + 6 unit Policy + 10 feature), 90 assertions total; lapisan Tag siap untuk modul Task/Project/Note/Habit.
