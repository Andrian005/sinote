# EPIC-004: Projects & Goals

- **ID:** EPIC-004
- **Judul:** Projects & Goals — Struktur Menengah & Jangka Panjang
- **Deskripsi:** Goal (berujung/berkelanjutan) dan Project dengan progres terhitung otomatis dari Task (FSD Modul 3).
- **Dependency:** EPIC-003 (Tasks).
- **Priority:** Must Have — MVP 1 (v0.2).
- **Estimasi:** 5–7 hari.
- **Status:** `Backlog`

## Acceptance Criteria

- [ ] Goal dapat dibuat dengan tipe `ended`/`ongoing`; tipe **immutable** setelah dibuat (divalidasi di Action, bukan hanya form).
- [ ] Project dapat dibuat, opsional ditautkan ke Goal.
- [ ] Progres Project dihitung otomatis dari rasio Task `done` — **tidak ada** input manual untuk field ini.
- [ ] `RecalculateProjectProgressAction` diuji unit test, termasuk skenario Project tanpa Task ("belum dimulai", bukan 0%).
- [ ] Listener `TaskCompleted` → update progres Project berfungsi.

## Checklist Sebelum Mulai

- [ ] EPIC-003 selesai penuh, termasuk unit test Event `TaskCompleted`.

## Checklist Setelah Selesai

- [ ] Unit test progres akurat terhadap perubahan status Task.
- [ ] Test eksplisit mencoba mengubah `Goal.type` dan memastikan ditolak.
- [ ] **Milestone 1 ("Bisa Dipakai") selesai setelah EPIC ini + EPIC-005 + EPIC-006.**

## Feature/Task Turunan

- FEAT — Goal Management (FSD 3.1)
- FEAT — Project Management (FSD 3.2)
