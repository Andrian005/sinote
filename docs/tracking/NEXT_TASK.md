# NEXT_TASK.md

> Antrian tiket berikutnya setelah `CURRENT_TASK.md` selesai, sudah diurutkan sesuai Coding Order & Dependency Map. **Jangan mengambil tiket dari sini di luar urutan** kecuali ada alasan eksplisit tercatat di `docs/decisions/DECISIONS.md`.

## Antrian (Sprint 4 — Tasks / EPIC-003)

**Status:** Sprint 3 (EPIC-002 Inbox) selesai penuh. FEAT-0003 kickoff selesai — 3 TASK siap dikerjakan.

**Urutan Eksekusi (sesuai Coding Order & Dependency Map):**

1. **TASK-0011** — Migration `tasks`, Enum TaskStatus + TaskPriority, Model Task
   - Dependency: FEAT-0003 (Done)
   - Estimasi: 0.5 hari
   - Scope: Migration (FK project_id tanpa constraint sementara), 2 Enum, Model Task dengan cast/scopes/relasi

2. **TASK-0012** — TaskFactory, TaskPolicy, Form Requests, Actions, Event + Unit Tests
   - Dependency: TASK-0011
   - Estimasi: 1.5 hari
   - Scope: Factory (9 states), Policy (7 methods), 3 Form Requests, 4 Actions + state machine guard, Event TaskCompleted + Listener stub, implementasi `CreatesTaskFromInbox` contract

3. **TASK-0013** — Livewire TaskList + TaskForm + Feature Tests + TaskSeeder
   - Dependency: TASK-0012
   - Estimasi: 1.5 hari
   - Scope: 2 Livewire components, halaman /tasks, Dashboard widget, 17+ feature tests, seeder

## Setelah Sprint 4 Selesai

Lanjut ke Sprint 5: EPIC-004 (Projects & Goals) sebagai kickoff FEAT-0004 — lihat `docs/planning/ROADMAP.md` untuk urutan prioritas.
