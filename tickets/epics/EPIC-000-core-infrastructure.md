# EPIC-000: Core Infrastructure

- **ID:** EPIC-000
- **Judul:** Core Infrastructure (Auth, User, Base Policy Pattern)
- **Deskripsi:** Fondasi wajib sebelum modul fitur apa pun dapat dibangun — mencakup setup environment, migration `users`, Auth (Breeze/Fortify), dan pola Policy dasar yang akan dipakai ulang seluruh entitas berikutnya.
- **Dependency:** Tidak ada (titik awal proyek).
- **Priority:** Must Have — blocking seluruh EPIC lain.
- **Estimasi:** 3–5 hari.
- **Status:** `Done`

## Acceptance Criteria

- [x] Environment lokal (Laravel, PostgreSQL, Redis) aktif dan diverifikasi.
- [x] Migration `users` sesuai Database Spec A.1.
- [x] Login/logout berfungsi via Breeze/Fortify.
- [x] Minimal satu Policy contoh diterapkan dan teruji manual.
- [x] Struktur folder `app/Domain/*` dasar sudah dibuat sesuai TDD bagian 3.

## Checklist Sebelum Mulai

- [x] Repository Git dengan branch strategy siap (`docs/rules/GIT_RULES.md`).
- [x] 6 dokumen acuan sudah disalin ke `docs/context/reference/`.

## Checklist Setelah Selesai

- [x] Seluruh Acceptance Criteria tercentang.
- [x] `docs/tracking/DONE.md` diperbarui.
- [x] EPIC-001 (Tagging/Context) siap dimulai — lihat `docs/tracking/NEXT_TASK.md`.

## Feature/Task Turunan

- TASK-0001 — Setup Environment Development Lokal
- TASK-0002 — Migration & Model `users`
- TASK-0003 — Auth (Breeze/Fortify) & Halaman Login
- TASK-0004 — Base Policy Pattern & Contoh Penerapan
