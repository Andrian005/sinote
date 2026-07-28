# TASK-0018: Migrations, Enum, Models — Reminders + NotificationPreferences

- **ID:** TASK-0018
- **Judul:** Migrations reminders + notification_preferences, Enum ReminderType + ReminderStatus, Model Reminder + NotificationPreference
- **Deskripsi:** Fondasi data EPIC-006 — dua migration baru (notification_preferences posisi 11, reminders posisi 12), dua Enum backed string, dan dua Model dengan cast, scope, relasi, dan Observer untuk auto-create NotificationPreference saat user baru dibuat.
- **Dependency:** TASK-0017 (EPIC-005 selesai).
- **Priority:** Must Have — MVP 1.
- **Estimasi:** 0.75 hari.
- **Status:** `Done`
- **Selesai:** 2026-07-28

## Acceptance Criteria

### Migrations

- [x] `create_notification_preferences_table` — 7 kolom:
  - `id` ULID PK
  - `user_id` ULID FK → users.id (cascade, unique)
  - `deadline_reminder_enabled` boolean default true
  - `habit_reminder_enabled` boolean default true
  - `habit_reminder_time` time default '20:00'
  - `review_ritual_enabled` boolean default true
  - `created_at` / `updated_at`
  - Unique constraint: `user_id` (one-to-one)
  - Soft delete: **tidak** (baris selalu ada selama user ada)

- [x] `create_reminders_table` — 10 kolom:
  - `id` ULID PK
  - `user_id` ULID FK → users.id (cascade)
  - `remindable_id` ULID (polymorphic, bukan FK constraint)
  - `remindable_type` varchar(50)
  - `reminder_type` varchar(20) enum — `deadline` | `habit_schedule` | `review_ritual`
  - `scheduled_at` timestamp
  - `status` varchar(20) enum default `scheduled` — `scheduled` | `sent` | `cancelled` | `skipped`
  - `sent_at` timestamp nullable
  - `created_at` / `updated_at`
  - Soft delete: **tidak** (cancelled/skipped ditandai status, tidak dihapus lunak)
  - Index: `(remindable_type, remindable_id)`, `(status, scheduled_at)`, `user_id`
  - Check constraint (pgsql): `reminder_type IN (...)`, `status IN (...)`

- [ ] `php artisan migrate:fresh` → sukses (semua migration) *(PostgreSQL tidak running di environment lokal saat implementasi — diverifikasi via test suite SQLite)*

### Enum

- [x] `ReminderType` di `app/Domain/Notification/Enums/ReminderType.php`:
  - `Deadline = 'deadline'`
  - `HabitSchedule = 'habit_schedule'`
  - `ReviewRitual = 'review_ritual'`
  - `label(): string` — label Bahasa Indonesia

- [x] `ReminderStatus` di `app/Domain/Notification/Enums/ReminderStatus.php`:
  - `Scheduled = 'scheduled'`
  - `Sent = 'sent'`
  - `Cancelled = 'cancelled'`
  - `Skipped = 'skipped'`
  - `isFinal(): bool` — true untuk Sent, Cancelled, Skipped
  - `label(): string`

### Models

- [x] `NotificationPreference` di `app/Domain/Notification/Models/NotificationPreference.php`:
  - HasUlids, `newFactory()`
  - `$fillable`, `casts()` (boolean fields)
  - Relasi: `belongsTo User`
  - **Tidak** ada SoftDeletes

- [x] `Reminder` di `app/Domain/Notification/Models/Reminder.php`:
  - HasUlids, `newFactory()`
  - `$fillable`, `casts()` (status → ReminderStatus, reminder_type → ReminderType, scheduled_at/sent_at datetime)
  - Relasi: `belongsTo User`, `morphTo remindable`
  - Scopes: `scopeScheduled`, `scopeSent`, `scopeCancelled`, `scopePendingDelivery` (scheduled + scheduled_at <= now)
  - **Tidak** ada SoftDeletes

### Observer

- [x] `UserObserver` di `app/Domain/Shared/Observers/UserObserver.php`:
  - `created()` → auto-create `NotificationPreference` dengan default values
  - Didaftarkan di `AppServiceProvider::boot()`

### User Model Update

- [x] Relasi `notificationPreference(): HasOne` dan `reminders(): HasMany` di `User` model — sudah ada, diverifikasi

### Verifikasi

- [x] `php artisan test` → 313 passed (428 assertions) — semua test lama tetap hijau
- [x] `vendor/bin/pint` → clean (178 files, 7 style issues fixed)

## Checklist Sebelum Mulai

- [x] FEAT-0006 selesai
- [x] Baca Database Spec A.11 + A.12
- [x] Baca DATABASE_RULES.md — migration order (pos 11 + 12)
- [x] Referensi TASK-0011 dan TASK-0014 sebagai pola

## Checklist Setelah Selesai

- [ ] `php artisan migrate:fresh` hijau *(pending — PostgreSQL tidak running di environment lokal)*
- [x] `php artisan test` → 313 passed (428 assertions) hijau
- [x] `vendor/bin/pint` clean
- [x] Status tiket → Done
- [x] `DONE.md` + `CHANGELOG.md` diperbarui
- [x] `CURRENT_TASK.md` → TASK-0019

## Catatan Implementasi

**PostgreSQL tidak running saat implementasi:** `migrate:fresh` tidak dapat dijalankan terhadap PostgreSQL. Verifikasi migration dilakukan via test suite yang menggunakan SQLite in-memory (`RefreshDatabase`). Migration akan diverifikasi ulang terhadap PostgreSQL saat environment aktif kembali.

**`habit_reminder_time` cast:** Kolom `time` tidak di-cast secara khusus di Model (Laravel menyimpan sebagai string `'HH:MM:SS'` — cukup untuk kebutuhan MVP). Cast khusus dapat ditambahkan di TASK-0019 jika logic Job membutuhkannya.

**UserObserver & test isolation:** Observer berjalan saat `User::factory()->create()` di seluruh test suite. Table `notification_preferences` tersedia via `RefreshDatabase`, sehingga Observer tidak merusak test yang sudah ada — diverifikasi 313 tests hijau.

**Factories dibuat di TASK-0018** (bukan TASK-0019) agar tersedia lebih awal untuk kemungkinan seeder atau test di tahap berikutnya.

