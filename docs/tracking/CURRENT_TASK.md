# CURRENT_TASK.md

> **File ini hanya boleh menunjuk ke SATU tiket aktif pada satu waktu.** AI dan developer wajib membaca file ini sebelum menulis kode apa pun (lihat `WORKFLOW.md`).

## Tiket Aktif Saat Ini

**ID:** FEAT-0004
**Judul:** Kickoff EPIC-004 — Projects & Goals
**File Lengkap:** *(belum dibuat — buat di awal sesi berikutnya)*
**EPIC Induk:** EPIC-004 — Projects & Goals
**Status:** `To Do`

> EPIC-003 (Tasks) selesai penuh. Sprint 5 dimulai dengan kickoff EPIC-004: baca FSD Modul 3 (Goals + Projects), Database Spec A.3 + A.4, pecah EPIC-004 menjadi TASK granular. Tambahkan juga FK constraint `project_id` pada tabel `tasks` (D-009) sebagai bagian dari EPIC-004.

## Langkah Kickoff

1. Buat tiket `tickets/features/FEAT-0004-kickoff-projects-goals.md`
2. Baca FSD Modul 3.1 (Goal Management) dan 3.2 (Project Management)
3. Baca Database Spec A.3 (goals) dan A.4 (projects)
4. Pecah EPIC-004 menjadi minimal 3 TASK: migration+enum+model, factory+policy+actions+tests, Livewire+feature tests+seeder
5. **Tambahkan migration untuk FK `tasks.project_id` → `projects.id`** (D-009 resolution)
6. Perbarui NEXT_TASK.md dengan antrian Sprint 5

---

*Setelah kickoff selesai: TASK pertama Sprint 5 menjadi CURRENT_TASK yang baru.*
