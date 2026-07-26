# EPIC-009: Focus Mode

- **ID:** EPIC-009
- **Judul:** Focus Mode — Eksekusi Bebas Distraksi
- **Deskripsi:** Overlay full-screen di atas satu Task, tanpa entitas database sendiri (FSD Modul 9, DECISIONS.md D-006).
- **Dependency:** EPIC-003 (Tasks) — **satu-satunya** dependency.
- **Priority:** Should Have — v0.4.
- **Estimasi:** 2–3 hari.
- **Status:** `Backlog`

## Acceptance Criteria

- [ ] Focus session dapat dimulai dari Task manapun (Dashboard/Project/All-Tasks).
- [ ] Tidak ada Migration/Model baru dibuat untuk fitur ini (murni Livewire + Blade di atas Model Task).
- [ ] Focus-trap keyboard diterapkan selama overlay aktif.
- [ ] "Tandai Selesai" di dalam Focus Mode memanggil `CompleteTaskAction` yang sama dengan modul Tasks (tidak ada logika duplikat).

## Checklist Sebelum Mulai

- [ ] EPIC-003 selesai penuh.

## Checklist Setelah Selesai

- [ ] Teruji manual dari 3 titik akses (Dashboard/Project/All-Tasks).
- [ ] **Milestone 2 ("Habit & Fokus") selesai setelah EPIC-007, EPIC-008, EPIC-009 selesai.**

## Feature/Task Turunan

- FEAT — Focus Session (FSD 9.1)
