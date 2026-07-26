# FEAT-0002: Kickoff EPIC-002 — Inbox / Capture

- **ID:** FEAT-0002
- **Judul:** Kickoff EPIC-002 (Inbox/Capture) — Pemecahan Menjadi TASK
- **Deskripsi:** Tiket transisi yang menandai EPIC-001 selesai dan memulai pemecahan EPIC-002 menjadi TASK-TASK konkret, mengikuti pola pemecahan sebelumnya (FEAT-0001 sebagai template). Memecah fitur Inbox/Capture menjadi implementasi migration, model, action, UI, dan testing secara bertahap.
- **Dependency:** TASK-0007 (EPIC-001 selesai penuh).
- **Priority:** Must Have — MVP 0 (v0.1).
- **Estimasi:** 0.5 hari (murni perencanaan/pemecahan tiket, bukan coding).
- **Status:** `Done`

## Acceptance Criteria

- [x] EPIC-002 dibaca ulang lengkap beserta FSD Modul 1 (Inbox/Capture).
- [x] Database Spec A.2 (tabel `inbox_items`) dibaca untuk memahami skema lengkap.
- [x] EPIC-002 dipecah menjadi minimal 3 TASK:
  - TASK-0008: Migration `inbox_items` & Model `InboxItem` dengan relasi
  - TASK-0009: InboxItemFactory, InboxItemPolicy, StoreInboxItemRequest, Actions (CreateInboxItem, TriageInboxItem, DiscardInboxItem) + unit tests
  - TASK-0010: Livewire Quick Capture Component, Inbox Triage Component, Feature Tests, Seeder
- [x] Setiap TASK baru dibuat dengan format standar (ID, Judul, Deskripsi, Acceptance Criteria, Dependency, Priority, Estimasi, Status, Checklist).
- [x] `docs/tracking/NEXT_TASK.md` diperbarui dengan antrian TASK Sprint 3.

## Checklist Sebelum Mulai

- [x] EPIC-001 berstatus `Done`.
- [x] FSD Modul 1 dibaca lengkap.
- [x] Database Spec A.2 dibaca lengkap.

## Checklist Setelah Selesai

- [x] TASK-0008 dijadikan `CURRENT_TASK.md` baru.
- [x] Pindahkan tiket ini ke `DONE.md`.

## Catatan

Pemecahan ini mengikuti Coding Order (DEVELOPMENT_PLAYBOOK.md § 5): Migration → Enum → Model → Factory → Policy → Form Request → Action → Event/Listener → Livewire → Feature Test → Seeder. Inbox memiliki state machine sederhana (`unprocessed → processed/discarded`) yang memerlukan Enum, sehingga TASK-0008 akan mencakup Enum status juga.

