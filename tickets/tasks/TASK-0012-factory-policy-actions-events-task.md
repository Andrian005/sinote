# TASK-0012: TaskFactory, Policy, Form Requests, Actions, Event + Unit Tests

- **ID:** TASK-0012
- **Judul:** TaskFactory, TaskPolicy, Form Requests, Actions Task, Event TaskCompleted + Unit Tests
- **Deskripsi:** Membuat seluruh lapisan logika bisnis Task: Factory untuk testing, Policy untuk authorization, Form Requests untuk validasi, 4 Actions (CreateTask, UpdateTask, UpdateTaskStatus, ArchiveTask) dengan state machine guard, Event TaskCompleted + Listener stub, dan unit tests lengkap. Termasuk implementasi nyata `CreatesTaskFromInbox` contract dari EPIC-002.
- **Dependency:** TASK-0011 (Model Task tersedia).
- **Priority:** Must Have — MVP 0 (v0.1).
- **Estimasi:** 1.5 hari.
- **Status:** `Done`
- **Selesai:** 2026-07-26

## Acceptance Criteria

### Factory
- [x] `TaskFactory` di `database/factories/Domain/Tasks/TaskFactory.php`:
  - State: `forUser`, `withProjectId`, `todo`, `inProgress`, `done` (completed_at=now), `archived`
  - State: `withTitle`, `overdue` (due_date=yesterday), `highPriority`, `lowPriority`, `withDescription`

### Policy
- [x] `TaskPolicy` di `app/Policies/TaskPolicy.php` — 7 methods:
  - `viewAny`: true; `view`: owner; `create`: true
  - `update`: owner + status bukan done/archived
  - `delete`: owner; `updateStatus`: owner; `archive`: owner + bukan archived
- [x] Terdaftar di `AuthServiceProvider`
- [x] `TaskPolicyTest` — 17 tests

### Form Requests
- [x] `StoreTaskRequest`: title required min:1 max:255, description nullable max:10000, priority nullable in:low/medium/high, due_date nullable date, project_id nullable string
- [x] `UpdateTaskRequest`: title `sometimes|required`, rules lainnya identik
- [x] `UpdateTaskStatusRequest`: status required in:todo/in_progress/done/archived

### Actions
- [x] `CreateTask`: trim title, default priority Medium, default status Todo, projectsTableExists() guard (skip sementara), completed_at=null
- [x] `UpdateTask`: strip status + completed_at dari data, trim title
- [x] `UpdateTaskStatus`: `TaskStatus::allowedTransitions()` guard, throw `InvalidTaskTransitionException`, set/clear completed_at, dispatch `TaskCompleted` saat → done
- [x] `ArchiveTask`: delegate ke UpdateTaskStatus(Archived)

### Exception
- [x] `InvalidTaskTransitionException` — message: "Cannot transition Task from '{from}' to '{to}'."

### Event & Listener
- [x] `TaskCompleted` event di `app/Domain/Tasks/Events/TaskCompleted.php` (Dispatchable + SerializesModels)
- [x] `UpdateProjectProgress` listener stub di `app/Listeners/UpdateProjectProgress.php` (Log::debug)
- [x] Didaftarkan via `EventServiceProvider` + `withProviders()` di `bootstrap/app.php`

### CreatesTaskFromInbox
- [x] `CreateTaskFromInbox` di `app/Domain/Tasks/Actions/CreateTaskFromInbox.php`:
  - content ≤255: title=content, description=null
  - content >255: title=first 255 chars, description=full content
- [x] Binding: `AppServiceProvider::register()` → `bind(CreatesTaskFromInbox::class, CreateTaskFromInboxAction::class)`

### Unit Tests
- [x] `CreateTaskTest` — 9 tests
- [x] `UpdateTaskTest` — 6 tests
- [x] `UpdateTaskStatusTest` — 17 tests (valid transitions, completed_at, event dispatch, invalid transitions, archived final)
- [x] `ArchiveTaskTest` — 4 tests
- [x] `TaskPolicyTest` — 17 tests
- [x] `CreateTaskFromInboxTest` — 4 tests
- [x] Total 57 tests baru + 4 triage tests (TriageInboxItemTest) kini menggunakan implementasi nyata = **168 tests (249 assertions)** hijau, pint clean

## Checklist Setelah Selesai

- [x] 57 unit tests baru hijau (target: 30+)
- [x] `php artisan test` → 168 passed (249 assertions)
- [x] `vendor/bin/pint` clean
- [x] Status tiket → Done
- [x] DONE.md + CHANGELOG.md diperbarui
- [x] CURRENT_TASK.md → TASK-0013

## Catatan Implementasi

**bootstrap/app.php — `withEvents(listen:...)`:** Sintaks named parameter tidak valid di versi ini. Solusi: gunakan `withProviders([EventServiceProvider::class])` — EventServiceProvider mendaftarkan `$listen` secara standar.

**`projectsTableExists()` di CreateTask:** Guard ownership project_id diimplementasikan dengan cek keberadaan tabel `projects` via `DB::getSchemaBuilder()->hasTable('projects')` — selama EPIC-003 berjalan tanpa tabel `projects`, guard di-skip otomatis. Akan aktif setelah EPIC-004.

**State machine di Enum:** `TaskStatus::allowedTransitions()` memusatkan logika transisi di satu tempat. `UpdateTaskStatus` cukup memanggil method ini — tidak ada duplikasi logika.
