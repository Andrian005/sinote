# FEAT-0003: Kickoff EPIC-003 — Tasks

- **ID:** FEAT-0003
- **Judul:** Kickoff EPIC-003 (Tasks) — Pemecahan Menjadi TASK
- **Deskripsi:** Tiket transisi yang menandai EPIC-002 selesai dan memulai pemecahan EPIC-003 menjadi TASK-TASK konkret. Mengikuti pola FEAT-0001 dan FEAT-0002 sebagai template. EPIC-003 mencakup CRUD Task, State Machine (todo/in_progress/done/archived), Event TaskCompleted, dan sambungan contract CreatesTaskFromInbox dari EPIC-002.
- **Dependency:** TASK-0010 (EPIC-002 selesai penuh).
- **Priority:** Must Have — MVP 0 (v0.1).
- **Estimasi:** 0.5 hari (murni perencanaan/pemecahan tiket, bukan coding).
- **Status:** `Done`
- **Selesai:** 2026-07-26

## Acceptance Criteria

- [x] EPIC-003 dibaca ulang lengkap beserta FSD Modul 2 (Tasks).
- [x] Database Spec A.5 (tabel `tasks`) dibaca untuk memahami skema lengkap.
- [x] Business Rules B.1 Task (state machine, cascade, constraint) dibaca.
- [x] EPIC-003 dipecah menjadi 3 TASK:
  - **TASK-0011:** Migration `tasks`, Enum TaskStatus + TaskPriority, Model Task
  - **TASK-0012:** TaskFactory, TaskPolicy, Form Requests (StoreTaskRequest + UpdateTaskRequest + UpdateTaskStatusRequest), Event TaskCompleted + Listener, Actions (CreateTask, UpdateTask, UpdateTaskStatus, ArchiveTask) + unit tests, implementasi `CreatesTaskFromInbox` contract
  - **TASK-0013:** Livewire TaskList + TaskForm components, Feature Tests, TaskSeeder
- [x] Setiap TASK baru dibuat dengan format standar.
- [x] `docs/tracking/NEXT_TASK.md` diperbarui dengan antrian TASK Sprint 4.

## Checklist Sebelum Mulai

- [x] EPIC-002 berstatus `Done`.
- [x] FSD Modul 2 dibaca lengkap.
- [x] Database Spec A.5 + Business Rules B.1 dibaca.

## Checklist Setelah Selesai

- [x] TASK-0011 dijadikan `CURRENT_TASK.md` baru.
- [x] Tiket ini dipindahkan ke `DONE.md`.

## Catatan

Pemecahan mengikuti Coding Order (DEVELOPMENT_PLAYBOOK.md § 5):
`Migration → Enum → Model → Factory → Policy → Form Request → Action → Event/Listener → Livewire → Feature Test → Seeder`

TASK-0012 lebih besar dari TASK-0009 karena mencakup: 2 Enum, state machine guard di Actions, Event + Listener, dan implementasi nyata `CreatesTaskFromInbox` contract. Estimasi 1.5 hari.

Task dapat berdiri bebas tanpa Project (project_id nullable, set null on delete). Ini berbeda dari pola sebelumnya dan perlu diperhatikan di unit test isolasi.
