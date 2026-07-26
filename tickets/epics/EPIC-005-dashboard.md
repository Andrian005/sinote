# EPIC-005: Dashboard / Today View

- **ID:** EPIC-005
- **Judul:** Dashboard — Agregasi "Apa yang Penting Sekarang"
- **Deskripsi:** Read-only aggregation layer menampilkan subset Task prioritas, Habit hari ini, dan reminder aktif (FSD Modul 5).
- **Dependency:** EPIC-003 (Tasks), EPIC-004 (Projects & Goals).
- **Priority:** Must Have — MVP 1.
- **Estimasi:** 3–4 hari.
- **Status:** `Backlog`

## Acceptance Criteria

- [ ] Dashboard menampilkan subset Task prioritas hari ini (maksimal 5–7 item), bukan seluruh Task.
- [ ] Placeholder Habit ditampilkan kosong dengan baik (EPIC-007 belum ada saat ini dimulai).
- [ ] Eager loading diterapkan (tidak ada N+1 — diverifikasi manual lewat query log).
- [ ] Empty state, loading state (skeleton per widget), error state (kegagalan parsial) diimplementasikan sesuai UI_RULES.md.

## Checklist Sebelum Mulai

- [ ] EPIC-004 selesai penuh.

## Checklist Setelah Selesai

- [ ] Dashboard menampilkan data nyata dari Task/Project.
- [ ] `docs/tracking/DONE.md` diperbarui.

## Feature/Task Turunan

- FEAT — Today Aggregation View (FSD 5.1)
