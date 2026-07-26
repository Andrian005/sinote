# EPIC-011: Full Notification Engine

- **ID:** EPIC-011
- **Judul:** Full Notification Engine — Reminder Habit & Ritual Review
- **Deskripsi:** Melengkapi Deadline Reminder dengan reminder jadwal Habit dan ritual Review (FSD Modul 11, DECISIONS.md D-005).
- **Dependency:** EPIC-006 (Deadline Reminder — lapisan dasar), EPIC-007 (Habit), EPIC-010 (Review).
- **Priority:** Could Have — v0.6.
- **Estimasi:** 5–7 hari.
- **Status:** `Backlog`

## Acceptance Criteria

- [ ] Reminder Habit terkirim jika belum check-in mendekati akhir hari, mengecek ulang kondisi tepat sebelum kirim (bukan hanya di awal job).
- [ ] Reminder ritual Review terkirim mendekati akhir periode jika Review belum diisi.
- [ ] Tidak ada duplikasi reminder untuk entitas yang sama dalam satu hari.
- [ ] Waktu kirim mengikuti `notification_preferences` per user.

## Checklist Sebelum Mulai

- [ ] EPIC-006, EPIC-007, EPIC-010 selesai penuh.

## Checklist Setelah Selesai

- [ ] Test skenario Habit dicentang tepat sebelum job berjalan (mencegah reminder salah kirim).
- [ ] `docs/tracking/DONE.md` diperbarui.

## Feature/Task Turunan

- FEAT — Habit Schedule Notifications (FSD 11.1)
- FEAT — Review Ritual Reminders (FSD 11.2)
