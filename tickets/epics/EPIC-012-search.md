# EPIC-012: Search

- **ID:** EPIC-012
- **Judul:** Search — Pencarian Lintas Modul
- **Deskripsi:** Read-only consumer yang mencari lintas Task/Project/Note/Habit dengan hasil terkategorikan (FSD Modul 12).
- **Dependency:** EPIC-003, EPIC-004, EPIC-007, EPIC-008 (seluruh sumber data harus ada).
- **Priority:** Could Have — v0.6.
- **Estimasi:** 3–4 hari.
- **Status:** `Backlog`

## Acceptance Criteria

- [ ] Query mencocokkan judul/isi Task, Project, Note, Habit sekaligus.
- [ ] Hasil terkategorikan per tipe entitas, difilter kepemilikan user.
- [ ] Query minimum 2 karakter.
- [ ] Kegagalan satu sumber data tidak menggagalkan hasil dari sumber lain.

## Checklist Sebelum Mulai

- [ ] EPIC-003, EPIC-004, EPIC-007, EPIC-008 selesai penuh.

## Checklist Setelah Selesai

- [ ] Test memastikan hasil selalu difilter kepemilikan user (tidak ada kebocoran data lintas user meski masih single-user).
- [ ] `docs/tracking/DONE.md` diperbarui.

## Feature/Task Turunan

- FEAT — Global Search (FSD 12.1)
