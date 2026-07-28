# NEXT_TASK.md

> Antrian tiket berikutnya setelah `CURRENT_TASK.md` selesai, sudah diurutkan sesuai Coding Order & Dependency Map. **Jangan mengambil tiket dari sini di luar urutan** kecuali ada alasan eksplisit tercatat di `docs/decisions/DECISIONS.md`.

## Antrian (Sprint 8 — Habit Tracking / EPIC-007)

**Status:** Sprint 7 (EPIC-006 Deadline Reminder) selesai penuh. **Milestone 1 (MVP v0.2) selesai.** FEAT-0007 kickoff menjadi tiket aktif.

**Urutan Eksekusi:**

1. **FEAT-0007** *(aktif)* — Kickoff EPIC-007 (Habit Tracking): baca FSD Modul 7, Database Spec A.7, pecah menjadi TASK granular
   - Dependency: EPIC-006 (Done)
   - Scope: pembuatan tiket TASK turunan, update NEXT_TASK.md

2. **TASK-0021** *(akan dibuat di FEAT-0007)* — Migrations habits + habit_logs, Enum HabitFrequency + HabitStatus, Model Habit + HabitLog
   - Dependency: FEAT-0007 (Done)
   - Estimasi: ~0.75 hari

3. **TASK-0022** *(akan dibuat di FEAT-0007)* — HabitFactory, HabitPolicy, Form Requests, Actions, streak calculation, unit tests
   - Dependency: TASK-0021 (Done)
   - Estimasi: ~1.5 hari

4. **TASK-0023** *(akan dibuat di FEAT-0007)* — Livewire HabitList + HabitLog component, Dashboard widget, Feature Tests, HabitSeeder
   - Dependency: TASK-0022 (Done)
   - Estimasi: ~1 hari

## Setelah Sprint 8 Selesai

Lanjut ke Sprint 9: EPIC-008 (Knowledge Base / Notes) — dependency: EPIC-003 (Tasks selesai), contract `CreatesNoteFromInbox` dari EPIC-002 siap disambungkan.
