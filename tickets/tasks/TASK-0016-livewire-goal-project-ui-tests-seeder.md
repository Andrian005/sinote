# TASK-0016: Livewire Goal & Project UI, Feature Tests, Seeders

- **ID:** TASK-0016
- **Judul:** Livewire GoalForm + GoalList + ProjectForm + ProjectList, Feature Tests, Seeders
- **Deskripsi:** Membuat antarmuka UI untuk Goals dan Projects: empat Livewire components, halaman /goals dan /projects, integrasi di Dashboard, feature tests end-to-end, dan Seeders. Ini adalah tiket UI akhir yang menyelesaikan EPIC-004.
- **Dependency:** TASK-0015 (Actions tersedia).
- **Priority:** Must Have — MVP 0 (v0.1).
- **Estimasi:** 2 hari.
- **Status:** `Done`
- **Selesai:** 2026-07-26

## Acceptance Criteria

### Livewire Components
- [x] `GoalForm` — create + edit, `isEditMode` readonly goal_type, `#[Validate]`, dispatch `goal-saved`, flash Alpine
- [x] `GoalList` — filter active/completed/archived, `withCount('projects')`, updateStatus (ownership guard), archive (Gate check), `#[On('goal-saved')]`, flash, pagination 10
- [x] `ProjectForm` — create + edit, `userGoals` computed (active Goals milik user untuk select), `#[Validate]`, dispatch `project-saved`, flash
- [x] `ProjectList` — filter active/completed/archived, limit widget mode, progress bar (`style="width: X%"`), `withCount('tasks')`, `with('goal')`, Gate check, `#[On('project-saved')]`, flash, pagination 10

### Routes & Halaman
- [x] `GET /goals` → `goals.index` — embed GoalForm + GoalList
- [x] `GET /projects` → `projects.index` — embed ProjectForm + ProjectList
- [x] Nav: hapus link "Tasks" sementara (TODO TASK-0013), ganti dengan "Projects & Goals" → `/projects` (desktop + mobile)

### Dashboard
- [x] `<livewire:projects.project-list :limit="3" />` widget "Projects" + link "Lihat Semua →"

### Feature Tests
- [x] `GoalFormTest` — 10 tests: create ongoing/timeBound, status active, reset, saved, validasi empty/max, edit title, goal_type immutable, edit other user
- [x] `GoalListTest` — 12 tests: visibility, isolasi, filter active, complete/reopen, flash, archive, auth guard update/archive other user, pagination
- [x] `ProjectFormTest` — 9 tests: create, status/progress, reset, saved, validasi, goal attachment, edit owner, edit other user
- [x] `ProjectListTest` — 13 tests: visibility, isolasi, filter, complete/reopen, flash, archive, auth guard x2, pagination, widget limit
- [x] 302 total tests (416 assertions) hijau, pint clean

### Seeders
- [x] `GoalSeeder` — 6 goals (2 timeBound active, 2 ongoing active, 1 completed, 1 archived)
- [x] `ProjectSeeder` — 6 projects + tasks milik project aktif
- [x] Keduanya terdaftar di `DatabaseSeeder`

## Checklist Setelah Selesai

- [x] 44 feature tests hijau (target: 34+)
- [x] `php artisan test` → 302 passed (416 assertions)
- [x] `vendor/bin/pint` clean (3 fixes)
- [x] Status tiket → Done, DONE.md + CHANGELOG.md diperbarui
- [x] EPIC-004 ditandai Done
- [x] CURRENT_TASK.md → FEAT-0005 (kickoff EPIC-005)

## Catatan Implementasi

**GoalList::updateStatus authorization:** Gate `'update'` di GoalPolicy hanya mengizinkan goal berstatus `active` — sehingga goal `completed` ditolak saat user ingin reopen. Solusi: ganti ke ownership check langsung (`$goal->user_id !== auth()->id()`) di component. State machine guard (allowed transitions) tetap di Action.

**assertDatabaseHas date format:** PostgreSQL menyimpan kolom `date` sebagai `"2026-10-26 00:00:00"` bukan `"2026-10-26"` saat di-query raw. Fix: tidak cek `target_date` di `assertDatabaseHas`, cek via Model cast `->target_date->toDateString()` sebagai gantinya.

**ProjectList widget:** Saat `limit > 0`, pagination disembunyikan dan query menggunakan `->limit()->get()` (Collection, bukan Paginator). Filter tabs juga disembunyikan di widget mode.
