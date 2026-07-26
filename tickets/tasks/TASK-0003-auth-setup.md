# TASK-0003: Auth (Breeze/Fortify) & Halaman Login

- **ID:** TASK-0003
- **Judul:** Auth (Breeze/Fortify) & Halaman Login
- **Deskripsi:** Memasang Laravel Breeze/Fortify untuk session-based authentication, mengikuti Authentication Flow di TDD bagian 18 — Sanctum disiapkan berdampingan untuk API masa depan (tidak dipakai di alur web utama).
- **Dependency:** TASK-0002.
- **Priority:** Must Have — blocking.
- **Estimasi:** 1 hari.
- **Status:** `Done`

## Acceptance Criteria

- [x] Breeze/Fortify terpasang, halaman login/register/logout berfungsi.
- [x] Rate limiting login (5 percobaan/menit per email+IP) aktif sesuai `docs/rules/SECURITY_RULES.md`.
- [x] Redirect setelah login sukses menuju placeholder `/today` (Dashboard belum ada — cukup route kosong/placeholder untuk saat ini, akan diisi EPIC-005).
- [x] Sanctum terpasang (`composer require laravel/sanctum` + config) namun **belum** dipakai di route web manapun.
- [x] CSRF protection bawaan tidak dinonaktifkan.

## Checklist Sebelum Mulai

- [x] TASK-0002 selesai.

## Checklist Setelah Selesai

- [x] Login/logout teruji manual.
- [x] Percobaan login gagal berulang memicu rate limit (teruji manual).
- [x] Commit: `core: add authentication via breeze/fortify`.
- [x] Pindahkan ke `DONE.md`, jadikan TASK-0004 sebagai `CURRENT_TASK.md` baru.
