# TASK-0013: Livewire Task UI, Feature Tests, TaskSeeder

- **ID:** TASK-0013
- **Judul:** Livewire TaskList + TaskForm Components, Feature Tests, TaskSeeder
- **Deskripsi:** Membuat antarmuka UI Task: dua Livewire components (TaskList untuk daftar + status update, TaskForm untuk create/edit), feature tests end-to-end, TaskSeeder untuk data development, dan integrasi di Dashboard. Ini adalah tiket UI akhir yang menyelesaikan EPIC-003.
- **Dependency:** TASK-0012 (Actions + Events tersedia).
- **Priority:** Must Have — MVP 0 (v0.1).
- **Estimasi:** 1.5 hari.
- **Status:** `Done`
- **Selesai:** 2026-07-26

## Acceptance Criteria

### Livewire Components

#### TaskForm
- [x] `TaskForm` di `app/Livewire/Tasks/TaskForm.php`:
  - `mount(?string $taskId)` — load task data jika edit mode, guard ownership
  - Properties: `$title`, `$description`, `$priority = 'medium'`, `$dueDate`, `$projectId`
  - `#[Validate]` per property
  - `save()` → CreateTask (create) atau UpdateTask (edit), dispatch `task-saved`, flash
  - `resetSaved()` dipanggil Alpine setelah 3 detik
- [x] View `resources/views/livewire/tasks/task-form.blade.php`:
  - Input title, textarea description, select priority (label Indonesia), date input due_date
  - Tombol "Simpan"/"Perbarui" dengan loading state
  - Flash sukses + error validasi

#### TaskList
- [x] `TaskList` di `app/Livewire/Tasks/TaskList.php`:
  - `mount(string $filter = 'active', int $limit = 0)`
  - `getTasksProperty()`: `orderByRaw` priority weight DESC + `due_date ASC NULLS LAST`; limit>0 → Collection, limit=0 → paginate(15)
  - `updateStatus(taskId, newStatus)` + `archive(taskId)` dengan Gate check
  - `#[On('task-saved')]` → `refreshList()`
  - Flash `$flash`/`$flashIsError` + `clearFlash()`
- [x] View `resources/views/livewire/tasks/task-list.blade.php`:
  - Filter tabs (hidden jika limit>0), priority badge (`badgeClass()`), status badge, due_date highlight terlambat
  - Dropdown aksi state-aware: Mulai / Tunda / Selesai / Buka Lagi / Arsipkan
  - Empty state per filter, pagination hanya jika limit=0

### Route & Halaman
- [x] `GET /tasks` → `tasks.index` → `livewire.pages.tasks.index` (embed TaskForm + TaskList)
- [x] Nav link "Tasks" sementara dengan TODO comment (desktop + mobile)

### Dashboard
- [x] `<livewire:tasks.task-list :limit="5" />` widget "Tugas Hari Ini" + link "Lihat Semua →"

### Feature Tests
- [x] `TaskFormTest` — 8 tests: create, status todo, reset, saved flag, validasi empty/max, edit owner, edit other user
- [x] `TaskListTest` — 13 tests: visibility, isolasi, filter active, todo→in_progress, in_progress→done, reopen, archive, auth guard, flash error auth, flash sukses update/archive, pagination, widget limit
- [x] 189 total tests (275 assertions) hijau, pint clean

### Seeder
- [x] `TaskSeeder`: 5 todo (1 overdue) + 3 in_progress + 3 done + 2 archived; terdaftar di DatabaseSeeder

## Checklist Setelah Selesai

- [x] 21 feature tests hijau (target: 17+)
- [x] `php artisan test` → 189 passed (275 assertions)
- [x] `vendor/bin/pint` clean
- [x] Status tiket → Done
- [x] DONE.md + CHANGELOG.md diperbarui
- [x] EPIC-003 ditandai Done
- [x] CURRENT_TASK.md → FEAT-0004

## Catatan Implementasi

**Widget mode:** `$limit > 0` mengubah `getTasksProperty()` dari paginator ke plain Collection — `$tasks->hasPages()` tidak berlaku; blade menggunakannya sebagai guard pagination. Filter tabs juga disembunyikan di widget mode.

**`#[On('task-saved')]`:** Livewire 3 attribute untuk listen cross-component event. `refreshList()` memanggil `resetPage()` + `unset($this->tasks)` agar computed property di-recalculate.

**`NULLS LAST` di PostgreSQL:** `orderByRaw('due_date ASC NULLS LAST')` agar task tanpa due_date muncul paling bawah. SQLite (testing) tidak support NULLS LAST — tidak masalah karena sorting hanya kosmetik dan tidak ditest secara eksplisit.
