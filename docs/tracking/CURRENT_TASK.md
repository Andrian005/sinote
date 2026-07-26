# CURRENT_TASK.md

> **File ini hanya boleh menunjuk ke SATU tiket aktif pada satu waktu.** AI dan developer wajib membaca file ini sebelum menulis kode apa pun (lihat `WORKFLOW.md`).

## Tiket Aktif Saat Ini

**ID:** FEAT-0002
**Judul:** Kickoff EPIC-002 (Inbox/Capture) — Pemecahan Menjadi TASK
**File Lengkap:** `tickets/features/FEAT-0002-kickoff-inbox.md` *(belum dibuat)*
**EPIC Induk:** EPIC-002 — Inbox / Capture
**Status:** `To Do`

> Tiket sebelumnya (EPIC-001 — Tagging & Context) sudah selesai penuh dengan 3 TASK (TASK-0005, TASK-0006, TASK-0007) dan dipindahkan ke DONE.md.
> Lapisan Tag siap digunakan oleh modul berikutnya.

## Ringkasan Cepat (Detail Lengkap di File Tiket)

Memecah EPIC-002 (Inbox) menjadi tiket TASK granular yang siap dikerjakan. Mengikuti pola FEAT-0001: membaca FSD Modul 1, Database Spec A.3, dan UI/UX Spec untuk Inbox; lalu memecah menjadi TASK dengan urutan: Migration → Model → Factory → Policy → Form Request → Action → Event/Listener → Livewire Component → Feature Test → Seeder.

## Sebelum Mulai — Sudah Dibaca?

- [ ] `docs/context/PROJECT_CONTEXT.md`
- [ ] `docs/rules/CORE_RULES.md`
- [ ] `docs/decisions/DECISIONS.md`
- [ ] File EPIC: `tickets/epics/EPIC-002-inbox.md`
- [ ] `docs/context/reference/02-fsd.md` — Modul 1 (Inbox)
- [ ] `docs/context/reference/04-database-business-rules-spec.md` — A.3 (Tabel `inbox_items`)
- [ ] `DEVELOPMENT_PLAYBOOK.md` § 6 — Ticket Workflow

---

*Setelah tiket ini selesai: tiket TASK baru dibuat di `tickets/tasks/`, NEXT_TASK.md diperbarui dengan antrian Sprint 3, lalu tiket TASK pertama menjadi `CURRENT_TASK.md` yang baru.*
