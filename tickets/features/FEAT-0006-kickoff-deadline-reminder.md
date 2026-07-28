# FEAT-0006: Kickoff EPIC-006 — Deadline Reminder

- **ID:** FEAT-0006
- **Judul:** Kickoff EPIC-006 (Deadline Reminder) — Pemecahan Menjadi TASK
- **Deskripsi:** Tiket transisi yang menandai EPIC-005 selesai dan memulai EPIC-006. Deadline Reminder adalah lapisan dasar notifikasi — mengingatkan user H-1/H untuk Task/Project berdeadline via scheduled job harian. EPIC-006 dipecah menjadi 3 TASK mengikuti Coding Order: migration + model, lalu actions + jobs + unit tests, lalu UI + feature tests.
- **Dependency:** TASK-0017 (EPIC-005 selesai penuh).
- **Priority:** Must Have — MVP 1.
- **Estimasi:** 0.5 hari (murni perencanaan).
- **Status:** `Done`
- **Selesai:** 2026-07-26

## Acceptance Criteria

- [x] EPIC-006 dibaca ulang beserta FSD Modul 6.1 (Deadline Reminder Scheduling & Delivery).
- [x] Database Spec A.11 (reminders), A.12 (notification_preferences), Business Rules relevan dibaca.
- [x] EPIC-006 dipecah menjadi 3 TASK:
  - **TASK-0018:** Migrations (reminders, notification_preferences), Enum ReminderType + ReminderStatus, Models Reminder + NotificationPreference
  - **TASK-0019:** ReminderFactory + NotificationPreferenceFactory, Policies, Actions (ScheduleDeadlineReminder, CancelDeadlineReminder), Jobs (ScanDeadlines, SendDeadlineReminder), Unit Tests
  - **TASK-0020:** Dashboard reminder widget, Livewire ReminderList component, Feature Tests, Seeder
- [x] `docs/tracking/NEXT_TASK.md` diperbarui dengan antrian Sprint 7.

## Catatan

**Satu tabel `reminders` untuk dua modul:** Tabel `reminders` dirancang bersama untuk Deadline Reminder (EPIC-006) dan Full Notification Engine (EPIC-011), dibedakan lewat `reminder_type`. Jangan buat tabel terpisah (D-005, Database Spec A.11).

**Job pattern:** Scanner + Sender dipisah — `ScanDeadlines` (dijadwalkan harian) memindai Task/Project berdeadline dalam H-1/H, lalu dispatch `SendDeadlineReminder` per entitas secara individual. Kegagalan satu Sender tidak menggagalkan batch (LARAVEL_RULES.md — Notification & Queue).

**Auto-cancel:** Reminder otomatis berstatus `cancelled` saat Task/Project berstatus `done`/`archived`/`completed` — dipicu via Event listener yang mendengarkan `TaskCompleted` (sudah ada) dan `ProjectStatusChanged` (baru di TASK-0019).

**NotificationPreference seeder:** Dibuat otomatis saat user baru dibuat via Observer `User::created` — bukan seeder terpisah (Database Spec H.2).

**Migration order:** `notification_preferences` (posisi 11) → `reminders` (posisi 12), sesuai urutan wajib di DATABASE_RULES.md.

Pemecahan mengikuti Coding Order:
`Migration → Enum → Model → Factory → Policy → Action → Job → Livewire → Feature Test → Seeder`

