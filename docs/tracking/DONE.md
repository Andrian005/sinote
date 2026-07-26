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

### EPIC-003 — Tasks (Unit Eksekusi Harian)
- Selesai: 2026-07-26
- File: `tickets/epics/EPIC-003-tasks.md`
- Catatan singkat: EPIC lengkap — 3 TASK (TASK-0011, TASK-0012, TASK-0013). State machine todo/in_progress/done/archived, Event TaskCompleted, CreatesTaskFromInbox contract tersambung, 21 feature tests baru → total 189 tests (275 assertions) hijau, pint clean.

### TASK-0013 — Livewire TaskForm + TaskList + Feature Tests + TaskSeeder
- Selesai: 2026-07-26
- File: `tickets/tasks/TASK-0013-livewire-task-ui-feature-tests-seeder.md`
- Catatan singkat: TaskForm (create/edit, #[Validate], dispatch task-saved), TaskList (filter active/done/archived, limit widget mode, pagination 15, #[On task-saved], Gate check), halaman /tasks, Dashboard widget TaskList limit=5, nav link Tasks sementara, TaskSeeder (5 todo+3 in_progress+3 done+2 archived), 8 TaskFormTest + 13 TaskListTest = 21 feature tests.

---

### TASK-0012 — TaskFactory, TaskPolicy, Form Requests, Actions, Event + Unit Tests
- Selesai: 2026-07-26
- File: `tickets/tasks/TASK-0012-factory-policy-actions-events-task.md`
- Catatan singkat: TaskFactory (11 state methods), TaskPolicy (7 methods), StoreTaskRequest + UpdateTaskRequest + UpdateTaskStatusRequest, InvalidTaskTransitionException, 4 Actions (CreateTask/UpdateTask/UpdateTaskStatus/ArchiveTask dengan state machine via `TaskStatus::allowedTransitions()`), Event TaskCompleted + Listener UpdateProjectProgress stub (daftar via EventServiceProvider + withProviders), CreateTaskFromInbox (implementasi nyata contract EPIC-002 + bind di AppServiceProvider); 54 unit tests baru → total 168 tests (249 assertions) hijau, pint clean.

---

### TASK-0011 — Migration `tasks`, Enum TaskStatus + TaskPriority, Model Task
- Selesai: 2026-07-26
- File: `tickets/tasks/TASK-0011-migration-model-task.md`
- Catatan singkat: Migration tasks (project_id tanpa FK constraint sementara — D-009), composite index (user_id,status,due_date), FK user_id restrict; Enum TaskStatus (4 cases + allowedTransitions/isActive) + TaskPriority (3 cases + weight/badgeClass/label); Model Task dengan cast, 7 scopes, relasi user/project/tags; 114 tests tetap hijau, pint clean.

---

### FEAT-0003 — Kickoff EPIC-003 (Tasks) — Pemecahan Menjadi TASK
- Selesai: 2026-07-26
- File: `tickets/features/FEAT-0003-kickoff-tasks.md`
- Catatan singkat: EPIC-003 dipecah menjadi TASK-0011 (migration+enum+model Task), TASK-0012 (factory+policy+actions+event+unit tests+CreatesTaskFromInbox), TASK-0013 (Livewire+feature tests+seeder); NEXT_TASK.md diperbarui Sprint 4; TASK-0011 menjadi tiket aktif.

---

### EPIC-002 — Inbox / Capture (Quick Capture & Triage)
- Selesai: 2026-07-26
- File: `tickets/epics/EPIC-002-inbox.md`
- Catatan singkat: EPIC lengkap — 3 TASK (TASK-0008, TASK-0009, TASK-0010) dengan 63 unit + feature tests baru; QuickCapture + InboxList Livewire components, route /inbox, dashboard widget, nav link Inbox, InboxItemSeeder; 114 tests total (183 assertions) hijau, pint clean.

### TASK-0010 — Livewire QuickCapture + InboxList + Feature Tests + Seeder
- Selesai: 2026-07-26
- File: `tickets/tasks/TASK-0010-livewire-inbox-capture-triage.md`
- Catatan singkat: QuickCapture component (validate, try/catch, flash), InboxList (pagination, Gate check, mock contracts via container), halaman /inbox, QuickCapture embed di dashboard, nav link Today+Inbox, InboxItemSeeder (7 unprocessed + 3 processed); 24 feature tests (9 QuickCapture + 15 InboxTriage) → 114 total tests hijau.

---

### TASK-0009 — InboxItemFactory, InboxItemPolicy, Form Requests, Actions + Unit Tests
- Selesai: 2026-07-26
- File: `tickets/tasks/TASK-0009-factory-policy-action-inbox-item.md`
- Catatan singkat: InboxItemFactory (5 state methods), InboxItemPolicy (6 methods termasuk triage), StoreInboxItemRequest + TriageInboxItemRequest, 3 Actions (CreateInboxItem, DiscardInboxItem, TriageInboxItem via contracts), exception InboxItemAlreadyProcessedException, 2 contracts sebagai stub untuk EPIC-003/005; 39 unit tests baru → total 91 tests (146 assertions) hijau, pint clean.

---

### TASK-0008 — Migration `inbox_items`, Enum InboxItemStatus, Model InboxItem
- Selesai: 2026-07-26
- File: `tickets/tasks/TASK-0008-migration-model-inbox-item.md`
- Catatan singkat: Migration inbox_items (posisi 3, setelah users, sebelum tags) dengan ULID, user_id FK, status enum, soft delete; Enum InboxItemStatus (Unprocessed/Processed/Discarded); Model InboxItem dengan cast, scopes, relasi belongsTo User; verifikasi via migrate:fresh dan tinker, 52 tests tetap hijau.

---

### FEAT-0002 — Kickoff EPIC-002 (Inbox/Capture) — Pemecahan Menjadi TASK
- Selesai: 2026-07-26
- File: `tickets/features/FEAT-0002-kickoff-inbox.md`
- Catatan singkat: EPIC-002 dipecah menjadi TASK-0008 (migration + enum + model), TASK-0009 (factory + policy + actions + unit test), TASK-0010 (Livewire Quick Capture + Inbox Triage + feature test + seeder); NEXT_TASK.md diperbarui dengan Sprint 3; TASK-0008 menjadi tiket aktif.

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
