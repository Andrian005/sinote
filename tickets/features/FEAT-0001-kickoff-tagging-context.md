# FEAT-0001: Kickoff EPIC-001 — Tagging & Context

- **ID:** FEAT-0001
- **Judul:** Kickoff EPIC-001 (Tagging/Context) — Pemecahan Menjadi TASK
- **Deskripsi:** Tiket transisi yang menandai EPIC-000 selesai dan memulai pemecahan EPIC-001 menjadi TASK-TASK konkret, mengikuti pola TASK-0001 s/d TASK-0004 sebagai template (lihat `docs/tracking/BACKLOG.md` § Cara Memecah EPIC).
- **Dependency:** TASK-0004 (EPIC-000 selesai penuh).
- **Priority:** Must Have.
- **Estimasi:** 0.5 hari (murni perencanaan/pemecahan tiket, bukan coding).
- **Status:** `Done`

## Acceptance Criteria

- [x] EPIC-001 dibaca ulang lengkap beserta FSD Modul 4.
- [x] EPIC-001 dipecah menjadi TASK-0005 (Migration `tags`+`taggables` & Model), TASK-0006 (Action Attach/Detach Tag + unit test), TASK-0007 (Livewire tag input component minimal, jika relevan sebelum ada entitas taggable sungguhan — atau ditunda jika lebih masuk akal diuji lewat unit test dulu).
- [x] Setiap TASK baru dibuat dengan format identik TASK-0001 s/d TASK-0004 (ID, Judul, Deskripsi, Acceptance Criteria, Dependency, Priority, Estimasi, Status, Checklist).
- [x] `docs/tracking/NEXT_TASK.md` diperbarui dengan antrian TASK-0005 dst.

## Checklist Sebelum Mulai

- [x] EPIC-000 berstatus `Done`.

## Checklist Setelah Selesai

- [x] TASK-0005 dijadikan `CURRENT_TASK.md` baru.
- [x] Pindahkan tiket ini ke `DONE.md`.

## Catatan

Tiket jenis ini (kickoff/pemecahan EPIC) berulang setiap kali sebuah EPIC baru akan dimulai — gunakan `AI_PROMPTS.md` § "Prompt Membuat Tiket Baru (Memecah EPIC)" untuk menghasilkan tiket serupa bagi EPIC-002 dan seterusnya.
