# EPIC-008: Knowledge Base

- **ID:** EPIC-008
- **Judul:** Knowledge Base — Catatan & Referensi
- **Deskripsi:** Arsip Note dengan auto-save dan linking opsional ke Project (FSD Modul 8).
- **Dependency:** EPIC-000, EPIC-001 (Tagging), EPIC-004 (Projects, untuk linking).
- **Priority:** Should Have — v0.4.
- **Estimasi:** 3–5 hari.
- **Status:** `Backlog`

## Acceptance Criteria

- [ ] Note dapat dibuat/diedit dengan auto-save (debounced, sesuai UI_RULES.md).
- [ ] Note dapat ditautkan/dilepas dari **satu** Project (bukan banyak).
- [ ] Menghapus Project yang ditautkan → `note.project_id` otomatis `null` (set-null on delete), Note **tidak** ikut hilang.
- [ ] Note dapat berdiri bebas tanpa Project.

## Checklist Sebelum Mulai

- [ ] EPIC-004 selesai penuh (untuk linking).

## Checklist Setelah Selesai

- [ ] Test menghapus Project memastikan Note tetap ada dengan `project_id = null`.
- [ ] `docs/tracking/DONE.md` diperbarui.

## Feature/Task Turunan

- FEAT — Note Creation & Management (FSD 8.1)
- FEAT — Note Linking to Projects (FSD 8.2)
