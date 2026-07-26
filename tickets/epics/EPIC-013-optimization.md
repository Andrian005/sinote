# EPIC-013: Optimization

- **ID:** EPIC-013
- **Judul:** Optimization — Performa & Cache
- **Deskripsi:** Audit query, index, dan cache berdasarkan pemakaian nyata setelah seluruh modul fitur selesai (TDD bagian 16, 28; Database Spec Bagian J).
- **Dependency:** EPIC-005 s/d EPIC-012 (seluruh modul fitur selesai — optimasi butuh query nyata untuk diukur, bukan asumsi).
- **Priority:** Must Have — sebelum v1.0.
- **Estimasi:** ~1 minggu.
- **Status:** `Backlog`

## Acceptance Criteria

- [ ] Query log Dashboard dan Search ditinjau, N+1 yang ditemukan diperbaiki.
- [ ] Index composite `(user_id, status, due_date)` pada `tasks` dan `(status, scheduled_at)` pada `reminders` terverifikasi ada dan terpakai (`EXPLAIN ANALYZE`).
- [ ] Cache Dashboard dengan invalidasi berbasis Event terverifikasi tidak menampilkan data basi.
- [ ] Partial index PostgreSQL untuk Task aktif dipertimbangkan/diterapkan jika relevan.

## Checklist Sebelum Mulai

- [ ] Seluruh EPIC modul fitur (005–012) selesai — **jangan optimasi preventif tanpa data nyata**.

## Checklist Setelah Selesai

- [ ] Waktu respons Dashboard dan Search terukur dan tercatat sebagai baseline.
- [ ] `docs/tracking/DONE.md` diperbarui.

## Feature/Task Turunan

*(Dipecah menjadi TASK teknis spesifik saat EPIC ini dimulai, berdasarkan temuan query log aktual — tidak dapat direncanakan detail di muka karena bergantung data nyata.)*
