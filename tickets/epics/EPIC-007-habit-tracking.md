# EPIC-007: Habit Tracking

- **ID:** EPIC-007
- **Judul:** Habit Tracking — Definisi & Check-in
- **Deskripsi:** Pelacakan kebiasaan berulang dengan streak (FSD Modul 7). **Independen dari Tasks/Projects** — dapat dikerjakan fleksibel kapan saja setelah EPIC-001.
- **Dependency:** EPIC-000, EPIC-001 (Tagging).
- **Priority:** Should Have — v0.3.
- **Estimasi:** 4–6 hari.
- **Status:** `Backlog`

## Acceptance Criteria

- [ ] Habit dapat didefinisikan (nama, frequency_type, frequency_target).
- [ ] Check-in harian tercatat di `habit_logs`, unique constraint `(habit_id, logged_date)` mencegah dobel.
- [ ] `RecalculateHabitStreakAction` akurat, termasuk skenario retroaktif (perubahan frekuensi **tidak** menghitung ulang streak lama).
- [ ] Habit muncul mengisi placeholder di Dashboard (EPIC-005).

## Checklist Sebelum Mulai

- [ ] EPIC-001 selesai (tidak perlu menunggu EPIC-003/004).

## Checklist Setelah Selesai

- [ ] Unit test streak (termasuk edge case perubahan frekuensi) hijau.
- [ ] `docs/tracking/DONE.md` diperbarui.

## Feature/Task Turunan

- FEAT — Habit Definition (FSD 7.1)
- FEAT — Habit Check-in & Streak Tracking (FSD 7.2)
