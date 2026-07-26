# NEXT_TASK.md

> Antrian tiket berikutnya setelah `CURRENT_TASK.md` selesai, sudah diurutkan sesuai Coding Order & Dependency Map. **Jangan mengambil tiket dari sini di luar urutan** kecuali ada alasan eksplisit tercatat di `docs/decisions/DECISIONS.md`.

## Antrian (Sprint 5 — Projects & Goals / EPIC-004)

**Status:** Sprint 4 (EPIC-003 Tasks) selesai penuh. FEAT-0004 kickoff selesai — 3 TASK siap dikerjakan.

**Urutan Eksekusi (sesuai Coding Order & Dependency Map):**

1. **TASK-0014** — Migrations (goals, projects, FK tasks.project_id), Enum GoalType + GoalStatus + ProjectStatus, Model Goal + Project
   - Dependency: FEAT-0004 (Done)
   - Estimasi: 0.75 hari
   - Scope: 2 migration baru + 1 migration alter (D-009 resolve), 3 Enum, 2 Model dengan cast/scopes/relasi; namespace `App\Domain\Projects\`

2. **TASK-0015** — GoalFactory + ProjectFactory, Policies, Form Requests, Actions, RecalculateProjectProgress, Unit Tests
   - Dependency: TASK-0014
   - Estimasi: 1.5 hari
   - Scope: 2 factories, 2 policies, 6 form requests, 8 actions + 2 exceptions, update `UpdateProjectProgress` listener stub → implementasi nyata, 70+ unit tests

3. **TASK-0016** — Livewire GoalForm + GoalList + ProjectForm + ProjectList, Feature Tests, Seeders
   - Dependency: TASK-0015
   - Estimasi: 2 hari
   - Scope: 4 Livewire components, halaman /goals + /projects, nav "Projects & Goals" primer (hapus link Tasks sementara), Dashboard widget ProjectList, 34+ feature tests, GoalSeeder + ProjectSeeder

## Setelah Sprint 5 Selesai

Lanjut ke Sprint 6: EPIC-005 (Knowledge Base / Notes) sebagai kickoff FEAT-0005.
