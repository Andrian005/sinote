# FEAT-0004: Kickoff EPIC-004 — Projects & Goals

- **ID:** FEAT-0004
- **Judul:** Kickoff EPIC-004 (Projects & Goals) — Pemecahan Menjadi TASK
- **Deskripsi:** Tiket transisi yang menandai EPIC-003 selesai dan memulai pemecahan EPIC-004 menjadi TASK konkret. EPIC-004 mencakup Goal Management (tipe berujung/berkelanjutan, state machine), Project Management (progres otomatis dari Task completion, state machine), dan resolusi D-009 (FK `tasks.project_id` → `projects.id`).
- **Dependency:** TASK-0013 (EPIC-003 selesai penuh).
- **Priority:** Must Have — MVP 0 (v0.1).
- **Estimasi:** 0.5 hari (murni perencanaan, bukan coding).
- **Status:** `Done`
- **Selesai:** 2026-07-26

## Acceptance Criteria

- [x] EPIC-004 dibaca ulang beserta FSD Modul 3.1 (Goal Management) dan 3.2 (Project Management).
- [x] Database Spec A.3 (goals), A.4 (projects), Business Rules B.2 (Project) dan B.3 (Goal) dibaca.
- [x] EPIC-004 dipecah menjadi 3 TASK:
  - **TASK-0014:** Migrations (goals, projects, FK tasks.project_id), Enum GoalType + GoalStatus + ProjectStatus, Models Goal + Project
  - **TASK-0015:** GoalFactory + ProjectFactory, Policies, Form Requests, Actions (CreateGoal, UpdateGoalStatus, CreateProject, UpdateProjectStatus, RecalculateProjectProgress), Event + Listener, Unit Tests
  - **TASK-0016:** Livewire GoalForm + GoalList + ProjectForm + ProjectList components, Feature Tests, Seeders
- [x] `docs/tracking/NEXT_TASK.md` diperbarui dengan antrian Sprint 5.

## Catatan

**D-009 Resolution:** Migration `add_fk_project_id_to_tasks_table` dibuat di TASK-0014 — menambahkan FK constraint `tasks.project_id` → `projects.id` (set null on delete) setelah tabel `projects` tersedia.

**RecalculateProjectProgress:** Implementasi nyata `UpdateProjectProgress` listener (stub di TASK-0012) dikerjakan di TASK-0015 — menghitung persentase Task `done` dari total Task milik Project.

**Goal type immutability:** `goal_type` tidak boleh diubah setelah Goal dibuat (FSD 3.1 — immutable field). Guard di `UpdateGoal` Action.

Pemecahan mengikuti Coding Order:
`Migration → Enum → Model → Factory → Policy → Form Request → Action → Event/Listener → Livewire → Feature Test → Seeder`
