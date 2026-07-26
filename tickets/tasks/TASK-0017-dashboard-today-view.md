# TASK-0017: Dashboard Today View — Livewire Component + Feature Tests

- **ID:** TASK-0017
- **Judul:** DashboardToday Livewire Component, Today Aggregation Logic, Feature Tests
- **Deskripsi:** Membuat Dashboard yang sesungguhnya sesuai FSD Modul 5.1 — menampilkan subset Task prioritas hari ini, progress Project aktif, hitungan Inbox unprocessed, dan placeholder Habit. Dashboard yang ada saat ini sudah punya widget QuickCapture (Inbox), TaskList (limit 5), dan ProjectList (limit 3) yang sudah fungsional. TASK-0017 memfokuskan pada **logika agregasi Today** yang benar: Task subset hari ini + overdue (bukan semua active), stats bar (Task today, Project active, Inbox unprocessed), dan empty/loading states yang proper.
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
  - Sort: priority DESC, lalu `due_date ASC NULLS LAST`
  - Limit: 7 item (FSD 5.1)
- [x] Stats bar: Task aktif (todo+in_progress), Project aktif, Inbox unprocessed
- [x] Habit placeholder card dengan pesan "Fitur Habit akan segera hadir"
- [x] Empty state Task: "Tidak ada task hari ini 🎉"

### Livewire Component
- [x] `DashboardToday` di `app/Livewire/Dashboard/DashboardToday.php`
- [x] `getTodayTasksProperty()` — whereDate + whereNull, limit 7, orderByRaw priority DESC
- [x] `getStatsProperty()` — 3 COUNT queries
- [x] `#[On('task-saved')]` + `#[On('project-saved')]` refresh
- [x] View `resources/views/livewire/dashboard/dashboard-today.blade.php`

### Update dashboard.blade.php
- [x] Disederhanakan menjadi single `<livewire:dashboard.dashboard-today />` embed

### Feature Tests
- [x] `DashboardTodayTest` — 11 tests hijau

## Checklist Setelah Selesai

- [x] 11 feature tests hijau
- [x] `php artisan test` → 313 passed (428 assertions)
- [x] `vendor/bin/pint` clean
- [x] Status tiket → Done
- [x] `DONE.md` + `CHANGELOG.md` diperbarui
- [x] EPIC-005 ditandai Done
- [x] `CURRENT_TASK.md` → FEAT-0006

## Catatan Implementasi

**`whereDate()` vs `where()` untuk date comparison:** SQLite tidak menangani `where('due_date', '<=', 'string')` secara konsisten untuk kolom `date`. Solusi: gunakan `orWhereDate('due_date', '<=', now()->toDateString())` — Laravel `whereDate()` membungkus kolom dengan fungsi `DATE()` yang bekerja di SQLite maupun PostgreSQL.

**dashboard.blade.php:** Disederhanakan dari multi-widget inline menjadi satu `<livewire:dashboard.dashboard-today />`. DashboardToday component menangani semua sub-widget secara internal (Quick Capture, Today Tasks, Projects embed, Habit placeholder).
  - Status: `todo` atau `in_progress`
  - Kriteria: `due_date <= today` ATAU `due_date IS NULL` (ditampilkan tapi di-sort terakhir)
  - Sort: priority DESC (`high` → `medium` → `low`), lalu `due_date ASC NULLS LAST`
  - Limit: maksimal 7 item (FSD 5.1)
- [ ] Stats bar di atas Dashboard:
  - "X Task aktif" — count `todo + in_progress` milik user
  - "X Project aktif" — count `active` milik user
  - "X di Inbox" — count `unprocessed` InboxItem milik user
- [ ] Placeholder Habit widget dengan pesan "Fitur Habit akan segera hadir" (EPIC-007 belum ada)
- [ ] Empty state per widget (Task: "Tidak ada task hari ini 🎉", Project: sudah ada)

### Livewire Component
- [ ] `DashboardToday` component di `app/Livewire/Dashboard/DashboardToday.php`:
  - Computed `getTodayTasksProperty()` — query Task dengan kriteria di atas
  - Computed `getStatsProperty()` — return array `['tasks' => X, 'projects' => X, 'inbox' => X]`
  - Refresh otomatis saat `task-saved` atau `project-saved` event diterima (via `#[On]`)
- [ ] View `resources/views/livewire/dashboard/dashboard-today.blade.php`:
  - Stats bar (3 angka)
  - Task list hari ini dengan priority badge + due date highlight
  - Project list sudah ada via `livewire:projects.project-list :limit="3"`
  - Habit placeholder card
  - Quick Capture tetap di atas

### Update dashboard.blade.php
- [ ] Embed `<livewire:dashboard.dashboard-today />` di halaman dashboard
- [ ] Layout: Stats bar → Quick Capture → Task Today → Projects → Habit placeholder

### Feature Tests
- [ ] `DashboardTodayTest` di `tests/Feature/Dashboard/DashboardTodayTest.php`:
  - ✓ Stats menampilkan jumlah Task aktif yang benar
  - ✓ Stats menampilkan jumlah Project aktif yang benar
  - ✓ Stats menampilkan jumlah Inbox unprocessed yang benar
  - ✓ Task overdue (past due_date + active) muncul di Today list
  - ✓ Task due hari ini muncul di Today list
  - ✓ Task due masa depan TIDAK muncul di Today list
  - ✓ Task tanpa due_date muncul (di-sort terakhir)
  - ✓ Task done/archived tidak muncul
  - ✓ Limit maksimal 7 Task
  - ✓ Task milik user lain tidak muncul
  - ✓ Stats hanya menghitung data milik user yang login

## Checklist Sebelum Mulai

- [ ] TASK-0016 selesai (EPIC-003 + EPIC-004 done)
- [ ] Baca FSD Modul 5.1 — Today Aggregation View
- [ ] Baca dashboard.blade.php saat ini untuk memahami layout existing

## Checklist Setelah Selesai

- [ ] 11+ feature tests hijau
- [ ] `php artisan test` — seluruh suite hijau
- [ ] `vendor/bin/pint` clean
- [ ] Status tiket → Done
- [ ] `DONE.md` + `CHANGELOG.md` diperbarui
- [ ] EPIC-005 ditandai Done
- [ ] `CURRENT_TASK.md` → FEAT-0006 (kickoff EPIC-006 Deadline Reminder atau EPIC-007 Habits sesuai prioritas)

## Catatan

**Dashboard sudah punya widget fungsional dari EPIC-002/003/004:**
- QuickCapture (Inbox) — aktif
- TaskList :limit=5 — menampilkan semua active Task (bukan hanya hari ini)
- ProjectList :limit=3 — aktif

**Yang ditambahkan TASK-0017:**
- Stats bar (agregasi angka)
- Filter TaskList Today menjadi "task hari ini + overdue" saja (bukan semua active)
- Habit placeholder card

**Pilihan implementasi:** Bisa modify TaskList component (tambah prop `todayOnly`) atau buat DashboardToday component baru yang mengandung semua aggregation logic. **Rekomendasi:** buat DashboardToday baru — lebih clean dan tidak merusak TaskList yang sudah dipakai di halaman /tasks.

**N+1 prevention:** Gunakan eager loading untuk Project → tasks count. Query stats dengan 3 query terpisah yang masing-masing ringan (count only) — tidak perlu join.
