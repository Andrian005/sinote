# FEAT-0005: Kickoff EPIC-005 — Dashboard / Today View

- **ID:** FEAT-0005
- **Judul:** Kickoff EPIC-005 (Dashboard / Today View) — Pemecahan Menjadi TASK
- **Deskripsi:** Tiket transisi yang menandai EPIC-004 selesai dan memulai EPIC-005. Dashboard / Today View adalah read-only aggregation layer — tidak ada migration atau model baru. EPIC-005 dipecah menjadi **satu TASK** karena scopenya terfokus: logika Today aggregation + stats bar + feature tests.
- **Dependency:** TASK-0016 (EPIC-004 selesai penuh).
- **Priority:** Must Have — MVP 1.
- **Estimasi:** 0.25 hari (murni perencanaan).
- **Status:** `Done`
- **Selesai:** 2026-07-26

## Acceptance Criteria

- [x] EPIC-005 dibaca ulang beserta FSD Modul 5.1 (Today Aggregation View).
- [x] EPIC-005 dipecah menjadi **1 TASK**:
  - **TASK-0017:** DashboardToday Livewire component, Today aggregation logic (Task hari ini + overdue, stats bar), Habit placeholder, Feature Tests
- [x] `docs/tracking/NEXT_TASK.md` diperbarui dengan antrian Sprint 6.

## Catatan

**Mengapa 1 TASK?** EPIC-005 tidak memerlukan migration, enum, atau model baru — semua data sudah ada dari EPIC-002/003/004. Satu TASK cukup untuk: komponen Livewire agregasi + logika query Today + feature tests. Ini berbeda dari EPIC sebelumnya yang butuh 3 TASK (migration → actions → UI).

**EPIC selanjutnya setelah EPIC-005:** EPIC-006 (Deadline Reminder) atau EPIC-007 (Habit Tracking) sesuai prioritas roadmap.
