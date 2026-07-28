# TASK-0020: Livewire ReminderList, Dashboard Widget, Feature Tests, Seeder

- **ID:** TASK-0020
- **Judul:** Dashboard reminder widget, Livewire ReminderList component, Feature Tests, ReminderSeeder
- **Deskripsi:** UI layer EPIC-006 — komponen Livewire untuk menampilkan reminder aktif di Dashboard, integrasi ke DashboardToday, feature tests end-to-end, dan seeder untuk development. Menyelesaikan EPIC-006.
- **Dependency:** TASK-0019 (actions + jobs tersedia).
- **Priority:** Must Have — MVP 1.
- **Estimasi:** 1 hari.
- **Status:** `Done`
- **Selesai:** 2026-07-28

## Acceptance Criteria

### Livewire Component

- [x] `ReminderList` di `app/Livewire/Notification/ReminderList.php`:
  - Property: `int $limit = 0`
  - `getRemindersProperty()` — ambil Reminder `scheduled` + `scheduled_at <= now` milik user, order by `scheduled_at ASC`, support limit
  - `#[On('reminder-updated')]` untuk refresh
  - View: `resources/views/livewire/notification/reminder-list.blade.php`

### Views

- [x] `livewire/notification/reminder-list.blade.php`:
  - Tampilkan per reminder: label entitas (judul Task/Project), tipe reminder (H-1/H), waktu scheduled_at
  - Badge urgensi: merah jika `scheduled_at` <= hari ini (H), kuning jika besok (H-1)
  - Empty state: "Tidak ada reminder aktif saat ini"
  - Mengikuti design system Tailwind yang sudah ada (UI_RULES.md)

### Dashboard Integration

- [x] Tambahkan `ReminderList` widget ke `DashboardToday` component:
  - `getRemindersCountProperty()` — count reminder `scheduled` + `scheduled_at <= now` milik user
  - Stats bar: tambahkan hitungan reminder aktif (amber badge, di samping stats inbox)
  - Embed `<livewire:notification.reminder-list :limit="5" />` di `dashboard-today.blade.php`
  - Posisi: di antara stats bar dan Quick Capture, atau setelah Today Tasks — sesuai prioritas visual

### Feature Tests

- [x] `ReminderListTest` di `tests/Feature/Notification/ReminderListTest.php`:
  - user melihat reminder scheduled miliknya
  - user tidak melihat reminder milik user lain
  - reminder sent/cancelled/skipped tidak muncul
  - reminder future (scheduled_at > now) tidak muncul
  - empty state muncul jika tidak ada reminder aktif
  - limit mode membatasi jumlah reminder
- [x] `DashboardTodayReminderTest` — stats bar menampilkan hitungan reminder aktif
- [x] `php artisan test` → seluruh suite hijau

### Seeder

- [x] `ReminderSeeder` di `database/seeders/ReminderSeeder.php`:
  - Buat 3 reminder `scheduled` (1 H hari ini, 1 H-1 besok, 1 past due tapi belum sent)
  - Buat 2 reminder `sent` (historis)
  - Daftarkan di `DatabaseSeeder`

### Scheduler Registration

- [x] `ScanDeadlines` job didaftarkan di Laravel Scheduler (`routes/console.php` atau `Console/Kernel.php`):
  - Jadwal: `->dailyAt('07:00')`
  - Deskripsi: `'Scan due deadlines and dispatch reminder jobs'`

## Checklist Sebelum Mulai

- [x] TASK-0019 selesai
- [x] Baca UI_RULES.md — Tailwind design system
- [x] Baca FSD 5.1 — Dashboard requirements (reminder ditampilkan di Dashboard)

## Checklist Setelah Selesai

- [x] Feature tests hijau
- [x] `php artisan test` → seluruh suite hijau (347 passed, 470 assertions)
- [x] `vendor/bin/pint` clean
- [x] Status tiket → Done
- [x] EPIC-006 ditandai Done di `tickets/epics/EPIC-006-deadline-reminder.md`
- [x] `DONE.md` + `CHANGELOG.md` diperbarui
- [x] **Milestone 1 selesai** — dogfooding harian penuh dimulai (MVP 1 / v0.2)
- [x] `CURRENT_TASK.md` → FEAT-0007 (Kickoff EPIC-007 Habit Tracking)

