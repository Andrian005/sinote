# EPIC-006: Deadline Reminder

- **ID:** EPIC-006
- **Judul:** Deadline Reminder (Lapisan Dasar Notifikasi)
- **Deskripsi:** Reminder otomatis H-1/H untuk Task/Project berdeadline — **hanya** bergantung pada Task/Project, tidak pada Habit (lihat DECISIONS.md D-005). FSD Modul 6.
- **Dependency:** EPIC-003 (Tasks), EPIC-004 (Projects & Goals), EPIC-005 (Dashboard, agar reminder langsung tampil).
- **Priority:** Must Have — MVP 1.
- **Estimasi:** 4–5 hari.
- **Status:** `Done`
- **Selesai:** 2026-07-28

## Acceptance Criteria

- [x] Migration `reminders` dan `notification_preferences` sesuai Database Spec A.11–A.12.
- [x] Job scanner harian memindai deadline jatuh dalam rentang H-1/H.
- [x] Job sender individual per Task — kegagalan satu tidak menggagalkan batch.
- [x] Reminder otomatis `cancelled` saat Task diselesaikan/diarsipkan sebelum waktunya.
- [x] Reminder tampil di Dashboard.

## Checklist Sebelum Mulai

- [x] EPIC-005 selesai penuh.

## Checklist Setelah Selesai

- [x] Reminder H-1/H terkirim dan tampil di Dashboard, teruji manual minimal 1 siklus.
- [x] **Milestone 1 selesai — dogfooding harian penuh dimulai (MVP 1 / v0.2).**

## Feature/Task Turunan

- FEAT — Deadline Reminder Scheduling & Delivery (FSD 6.1)
