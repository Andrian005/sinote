# TASK-0019: Factory, Policy, Actions, Jobs, Unit Tests — Deadline Reminder

- **ID:** TASK-0019
- **Judul:** ReminderFactory + NotificationPreferenceFactory, Policies, Actions (ScheduleDeadlineReminder, CancelDeadlineReminder), Jobs (ScanDeadlines, SendDeadlineReminder), Unit Tests
- **Deskripsi:** Logika bisnis EPIC-006 — Actions untuk menjadwalkan dan membatalkan reminder, Jobs untuk scanner + sender harian, Event listener untuk auto-cancel saat Task/Project selesai, dan unit tests lengkap.
- **Dependency:** TASK-0018 (migrations + models tersedia).
- **Priority:** Must Have — MVP 1.
- **Estimasi:** 1.5 hari.
- **Status:** `Done`
- **Selesai:** 2026-07-28

## Acceptance Criteria

### Factories

- [x] `ReminderFactory` — state methods: `scheduled()`, `sent()`, `cancelled()`, `skipped()`, `forTask()`, `dueToday()`, `dueTomorrow()` *(dibuat di TASK-0018 agar tersedia lebih awal)*
- [x] `NotificationPreferenceFactory` — state methods: `withDeadlineDisabled()`, `withHabitDisabled()`, `withReviewDisabled()` *(dibuat di TASK-0018)*

### Policies

- [x] `ReminderPolicy` — `view`: `$user->id === $reminder->user_id`
- [x] Daftarkan di `AuthServiceProvider`

### Actions

- [x] `ScheduleDeadlineReminder` di `app/Domain/Notification/Actions/`:
  - Input: Task|Project model
  - Guard: idempotent — skip jika `scheduled` reminder sudah ada untuk tanggal yang sama
  - Buat dua `Reminder`: H-1 (`scheduled_at = due_date - 1 day 08:00`), H (`scheduled_at = due_date 08:00`)
  - Skip H-1 jika due_date = hari ini (window sudah lewat)
  - Skip seluruhnya jika due_date null

- [x] `CancelDeadlineReminder` di `app/Domain/Notification/Actions/`:
  - Input: Task|Project model
  - Bulk update semua `scheduled` reminder → `cancelled` untuk entitas tersebut
  - Idempotent — aman dipanggil berulang kali

### Jobs

- [x] `ScanDeadlines` di `app/Jobs/`:
  - Query Reminder `scheduled` untuk Task active + Project active due today/tomorrow
  - Filter user dengan `deadline_reminder_enabled = true`
  - Dispatch `SendDeadlineReminder` per Reminder (scanner + sender pattern)
  - Terdaftar di scheduler: `dailyAt('07:00')` di `routes/console.php`

- [x] `SendDeadlineReminder` di `app/Jobs/`:
  - Terima `reminderId` string (bukan model — hindari stale data)
  - Guard: skip jika Reminder null atau `status->isFinal()`
  - Update status → `sent`, set `sent_at = now()`

### Event Listener

- [x] `CancelRemindersOnTaskCompleted` listener:
  - Mendengarkan `TaskCompleted` event
  - Skip jika Task tidak punya `due_date`
  - Panggil `CancelDeadlineReminder`
  - Terdaftar di `EventServiceProvider`

- [x] `ProjectStatusChanged` event di `app/Domain/Projects/Events/`:
  - Dispatch dari `UpdateProjectStatus` Action saat status → `completed` atau `archived`
  - Membawa `Project` dan `ProjectStatus $newStatus`

- [x] `CancelRemindersOnProjectStatusChanged` listener:
  - Mendengarkan `ProjectStatusChanged` event
  - Skip jika Project tidak punya `due_date`
  - Panggil `CancelDeadlineReminder`
  - Terdaftar di `EventServiceProvider`

### Unit Tests

- [x] `ScheduleDeadlineReminderTest` — 7 tests: dua reminder dibuat, H-1 jam 08:00, H jam 08:00, skip H-1 jika due today, skip jika no due_date, idempotent, user association benar
- [x] `CancelDeadlineReminderTest` — 4 tests: cancel semua scheduled, tidak cancel final state, idempotent pada empty, tidak cancel entitas lain
- [x] `SendDeadlineReminderTest` — 5 tests: marks sent + sent_at set, skip jika sent, skip jika cancelled, skip jika skipped, safe jika ID tidak ada
- [x] `CancelRemindersOnTaskCompletedTest` — 3 tests: cancel via listener, skip jika no due_date, triggered via UpdateTaskStatus
- [x] `CancelRemindersOnProjectStatusChangedTest` — 4 tests: cancel saat completed, cancel saat archived, skip jika no due_date, triggered via UpdateProjectStatus
- [x] `php artisan test` → 336 passed (459 assertions)

## Checklist Sebelum Mulai

- [x] TASK-0018 selesai
- [x] Baca FSD 6.1 (Deadline Reminder Scheduling & Delivery)
- [x] Baca LARAVEL_RULES.md — Job pattern scanner + sender

## Checklist Setelah Selesai

- [x] Unit tests hijau — 23 tests baru, 336 total passed (459 assertions)
- [x] `php artisan test` → seluruh suite hijau
- [x] `vendor/bin/pint` → clean (191 files, 7 style issues fixed)
- [x] Status tiket → Done
- [x] `DONE.md` + `CHANGELOG.md` diperbarui
- [x] `CURRENT_TASK.md` → TASK-0020

## Catatan Implementasi

**ReminderFactory dibuat di TASK-0018:** Factory dibuat lebih awal agar tersedia saat seeder atau test awal dibutuhkan. Tidak ada duplikasi — factory cukup di satu tempat.

**ProjectStatusChanged dispatch:** Hanya di-dispatch untuk transisi ke `completed` atau `archived` — transisi ke `active` (reopen) tidak perlu cancel reminder karena Project yang dibuka kembali mungkin masih punya deadline yang valid.

**ScanDeadlines query strategy:** Query dilakukan terhadap tabel `reminders` (bukan scan langsung Task/Project), sehingga hanya Reminder yang sudah dijadwalkan via `ScheduleDeadlineReminder` yang diproses. Ini menjaga konsistensi — tidak ada reminder yang dikirim tanpa melalui scheduling pipeline.

**forProject state di ReminderFactory:** Tidak diimplementasikan di TASK-0018 karena memerlukan ProjectFactory yang sudah ada di TASK-0015. Factory `forTask` cukup untuk seluruh unit test TASK-0019. Test project reminder menggunakan raw `Reminder::factory()->create([...])`.
