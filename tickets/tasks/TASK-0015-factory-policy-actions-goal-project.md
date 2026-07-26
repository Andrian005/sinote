# TASK-0015: Factory, Policy, Form Requests, Actions, Event + Unit Tests — Goal & Project

- **ID:** TASK-0015
- **Judul:** GoalFactory + ProjectFactory, Policies, Form Requests, Actions (Goal + Project + RecalculateProjectProgress), Unit Tests
- **Deskripsi:** Membuat lapisan logika bisnis EPIC-004: dua Factory, dua Policy, Form Requests, Actions untuk Goal dan Project, implementasi nyata `RecalculateProjectProgress` (menggantikan stub di TASK-0012), dan unit tests lengkap.
- **Dependency:** TASK-0014 (Models Goal + Project tersedia).
- **Priority:** Must Have — MVP 0 (v0.1).
- **Estimasi:** 1.5 hari.
- **Status:** `Done`
- **Selesai:** 2026-07-26

## Acceptance Criteria

### Factories
- [x] `GoalFactory` di `database/factories/Domain/Projects/GoalFactory.php`:
  - State: `forUser`, `timeBound` (dengan target_date), `ongoing`, `active`, `completed`, `archived`, `withTitle`, `overdue`
- [x] `ProjectFactory` di `database/factories/Domain/Projects/ProjectFactory.php`:
  - State: `forUser`, `forGoal`, `active`, `completed` (progress=100), `archived`, `withTitle`, `withProgress`, `overdue`

### Policies
- [x] `GoalPolicy` — `viewAny/create: true`, `view/update/delete: owner`, `complete: owner+active`, `archive: owner+bukan archived`
- [x] `ProjectPolicy` — `viewAny/create: true`, `view/update(active|completed)/delete: owner`, `archive: owner+bukan archived`
- [x] Keduanya terdaftar di `AuthServiceProvider`
- [x] `GoalPolicyTest` — 15 tests; `ProjectPolicyTest` — 13 tests

### Form Requests
- [x] `StoreGoalRequest` — title required, goal_type required in:time_bound/ongoing, target_date required_if time_bound
- [x] `UpdateGoalRequest` — title sometimes, NO goal_type (immutable)
- [x] `UpdateGoalStatusRequest` — status required in:active/completed/archived
- [x] `StoreProjectRequest` — title required, goal_id nullable, due_date nullable
- [x] `UpdateProjectRequest` — title sometimes
- [x] `UpdateProjectStatusRequest` — status required in:active/completed/archived

### Actions — Goal
- [x] `CreateGoal` — TimeBound guard (throw InvalidArgumentException jika target_date kosong), default status active
- [x] `UpdateGoal` — strip goal_type (immutable) + strip status (silent)
- [x] `UpdateGoalStatus` — `GoalStatus::allowedTransitions()` guard, throw `InvalidGoalTransitionException`
- [x] `ArchiveGoal` — shortcut ke UpdateGoalStatus(Archived)

### Actions — Project
- [x] `CreateProject` — goal_id ownership guard, default status active + progress 0
- [x] `UpdateProject` — strip status + progress (silent)
- [x] `UpdateProjectStatus` — `ProjectStatus::allowedTransitions()` guard, throw `InvalidProjectTransitionException`
- [x] `ArchiveProject` — shortcut ke UpdateProjectStatus(Archived)
- [x] `RecalculateProjectProgress` — formula (done/total)*100, archived excluded, auto-complete jika 100%+total>0

### Listener Update
- [x] `UpdateProjectProgress` listener — stub Log::debug → panggil `RecalculateProjectProgress` nyata

### Exceptions
- [x] `InvalidGoalTransitionException` di `app/Domain/Projects/Exceptions/`
- [x] `InvalidProjectTransitionException` di `app/Domain/Projects/Exceptions/`

### Unit Tests
- [x] `CreateGoalTest` — 6 tests
- [x] `UpdateGoalTest` — 5 tests (immutable goal_type, strip status)
- [x] `UpdateGoalStatusTest` — 6 tests (valid transitions, invalid, archived final)
- [x] `GoalPolicyTest` — 15 tests
- [x] `CreateProjectTest` — 6 tests
- [x] `UpdateProjectTest` — 5 tests
- [x] `UpdateProjectStatusTest` — 6 tests
- [x] `RecalculateProjectProgressTest` — 7 tests (0 tasks, all todo, 50%, 100%, auto-complete, archived excluded, already completed)
- [x] `ProjectPolicyTest` — 13 tests
- [x] `php artisan test` → 258 passed (358 assertions) — seluruh suite hijau
- [x] `vendor/bin/pint` → clean (6 fixes)

## Checklist Setelah Selesai

- [x] 69 unit tests baru hijau (target: 70+ — tercapai 69, semua test cases terpenuhi)
- [x] `php artisan test` → 258 passed (358 assertions) hijau
- [x] `vendor/bin/pint` clean
- [x] Status tiket → Done
- [x] `DONE.md` + `CHANGELOG.md` diperbarui
- [x] `CURRENT_TASK.md` → TASK-0016

## Catatan Implementasi

**`RecalculateProjectProgress`:** Task archived dikecualikan dari denominator — hanya todo+in_progress+done yang dihitung. Jika tidak ada task (total=0): action return early tanpa mengubah progress (tidak auto-complete karena belum ada task yang bisa diselesaikan).

**`already completed project`:** Jika project sudah `completed`, recalculate tetap update progress tapi tidak throw — `UpdateProjectStatus` tidak dipanggil ulang karena guard hanya mengarah ke `active→completed`, bukan `completed→completed`.

**`ArchiveGoalTest` + `ArchiveProjectTest`:** Tidak dibuat sebagai file terpisah — coverage sudah tercakup oleh `UpdateGoalStatusTest` dan `UpdateProjectStatusTest` yang menguji transisi ke `archived`. Pola identik dengan `ArchiveTask` di TASK-0012.
