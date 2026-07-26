# EPIC-010: Review & Reflection

- **ID:** EPIC-010
- **Judul:** Review & Reflection — Ritual Refleksi Berkala
- **Deskripsi:** Daily/Weekly/Monthly Review dengan snapshot metrik yang dibekukan permanen (FSD Modul 10).
- **Dependency:** EPIC-003 (Tasks), EPIC-004 (Projects & Goals), EPIC-007 (Habit — butuh data historis).
- **Priority:** Should Have — v0.5.
- **Estimasi:** 4–6 hari.
- **Status:** `Backlog`

## Acceptance Criteria

- [ ] Daily Review: satu entri per tanggal per user, agregasi Task selesai + Habit tercentang.
- [ ] Weekly/Monthly Review: agregasi periode terkait.
- [ ] `snapshot_metrics` **tidak berubah** setelah dibuat, meski data sumber berubah kemudian (test eksplisit wajib).
- [ ] Reflection note auto-save, boleh kosong.
- [ ] Periode tanpa aktivitas menampilkan "belum ada aktivitas", bukan error/nol yang menyesatkan.

## Checklist Sebelum Mulai

- [ ] EPIC-007 selesai (Habit harus punya data historis untuk diagregasi).

## Checklist Setelah Selesai

- [ ] Test snapshot-freeze hijau.
- [ ] Minimal satu siklus Weekly Review sudah didogfooding.
- [ ] **Milestone 3 ("Reflektif") selesai.**

## Feature/Task Turunan

- FEAT — Daily Review (FSD 10.1)
- FEAT — Weekly & Monthly Review (FSD 10.2)
