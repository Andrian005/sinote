# TASK-0017: Dashboard Today View — Livewire Component + Feature Tests

- **ID:** TASK-0017
- **Judul:** DashboardToday Livewire Component, Today Aggregation Logic, Feature Tests
- **Deskripsi:** Membuat Dashboard yang sesungguhnya sesuai FSD Modul 5.1 — menampilkan subset Task prioritas hari ini, progress Project aktif, hitungan Inbox unprocessed, dan placeholder Habit. TASK-0017 memfokuskan pada **logika agregasi Today** yang benar: Task subset hari ini + overdue (bukan semua active), stats bar (Task active, Project active, Inbox unprocessed), dan empty/loading states yang proper.
- **Dependency:** TASK-0016 (EPIC-003 + EPIC-004 selesai; Task, Project, InboxItem models tersedia).
- **Priority:** Must Have — MVP 1.
- **Estimasi:** 1 hari.
- **Status:** `Done`
- **Selesai:** 2026-07-26

## Acceptance Criteria

### Dashboard Today Logic
- [x] Dashboard hanya menampilkan Task yang **relevan hari ini**:
  - Status: `todo` atau `in_progress`
  - Kriteria: `due_date <= today` (via `whereDate`) ATAU `due_date IS NULL`
  - Sort: priority DESC (`high` → `medium` → `low`), lalu `due_date ASC NULLS LAST`
  - Limit: maksimal 7 item (FSD 5.1)
- [x] Stats bar di atas Dashboard:
  - "X Task aktif" — count `todo + in_progress` milik user
  - "X Project aktif" — count `active` milik user
  - "X di Inbox" — count `unprocessed` InboxItem milik user
- [x] Placeholder Habit widget dengan pesan "Fitur Habit akan segera hadir" (EPIC-007 belum ada)
- [x] Empty state Task: "Tidak ada task hari ini 🎉"

### Livewire Component
- [x] `DashboardToday` di `app/Livewire/Dashboard/DashboardToday.php`:
  - `getTodayTasksProperty()` — whereDate + whereNull, limit 7, orderByRaw priority DESC
  - `getStatsProperty()` — 3 COUNT queries (tasks/projects/inbox)
  - `#[On('task-saved')]` + `#[On('project-saved')]` untuk refresh computed
- [x] View `resources/views/livewire/dashboard/dashboard-today.blade.php`:
  - Stats bar (3 angka berwarna: biru/ungu/amber)
  - Quick Capture embed
  - Today task list dengan priority badge + status badge + due date highlight (merah = terlambat)
  - Project list embed via `livewire:projects.project-list :limit="3"`
  - Habit placeholder card

### Update dashboard.blade.php
- [x] Disederhanakan menjadi single `<livewire:dashboard.dashboard-today />` embed
- [x] Layout yang diimplementasikan: Stats bar → Quick Capture → Task Today → Projects → Habit placeholder

### Feature Tests
- [x] `DashboardTodayTest` di `tests/Feature/Dashboard/DashboardTodayTest.php` — 11 tests:
  - [x] stats menampilkan jumlah Task aktif yang benar
  - [x] stats menampilkan jumlah Project aktif yang benar
  - [x] stats menampilkan jumlah Inbox unprocessed yang benar
  - [x] stats hanya menghitung data milik user yang login
  - [x] Task overdue (past due_date + active) muncul di Today list
  - [x] Task due hari ini muncul di Today list
  - [x] Task due masa depan TIDAK muncul di Today list
  - [x] Task tanpa due_date muncul di Today list
  - [x] Task done dan archived tidak muncul di Today list
  - [x] Task milik user lain tidak muncul di Today list
  - [x] Today list dibatasi maksimal 7 Task

## Checklist Sebelum Mulai

- [x] TASK-0016 selesai (EPIC-003 + EPIC-004 done)
- [x] Baca FSD Modul 5.1 — Today Aggregation View
- [x] Baca dashboard.blade.php saat ini untuk memahami layout existing

## Checklist Setelah Selesai

- [x] 11 feature tests hijau (target: 11+)
- [x] `php artisan test` → 313 passed (428 assertions) — seluruh suite hijau
- [x] `vendor/bin/pint` → clean (1 fix)
- [x] Status tiket → Done
- [x] `DONE.md` + `CHANGELOG.md` diperbarui
- [x] EPIC-005 ditandai Done
- [x] `CURRENT_TASK.md` → FEAT-0006

## Catatan Implementasi

**`whereDate()` vs `where()` untuk date comparison:** SQLite tidak menangani `where('due_date', '<=', 'string')` secara konsisten untuk kolom `date`. Solusi: gunakan `orWhereDate('due_date', '<=', now()->toDateString())` — Laravel `whereDate()` membungkus kolom dengan fungsi `DATE()` yang bekerja di SQLite maupun PostgreSQL. Ini menjadi konvensi proyek untuk semua perbandingan date column di testing.

**dashboard.blade.php:** Disederhanakan dari multi-widget inline menjadi satu `<livewire:dashboard.dashboard-today />`. DashboardToday component menangani semua sub-widget secara internal (Quick Capture, Today Tasks, Projects embed, Habit placeholder) — lebih clean dan tidak merusak TaskList/ProjectList yang sudah dipakai di halaman /tasks dan /projects.

**Tidak ada DashboardToday test untuk sorting:** Sorting `NULLS LAST` di PostgreSQL tidak ditest secara eksplisit karena SQLite tidak mendukung sintaks tersebut. Perilaku sorting hanya relevan di production.
