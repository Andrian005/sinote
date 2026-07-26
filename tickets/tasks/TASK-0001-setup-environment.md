# TASK-0001: Setup Environment Development Lokal

- **ID:** TASK-0001
- **Judul:** Setup Environment Development Lokal
- **Deskripsi:** Menyiapkan Laravel + PostgreSQL + Redis di lingkungan lokal, menginisialisasi repository Git dengan branch strategy, dan membuat struktur folder `app/Domain/*` dasar sesuai TDD bagian 3 — sebagai prasyarat mutlak sebelum EPIC-000 dapat dilanjutkan ke TASK berikutnya.
- **Dependency:** Tidak ada.
- **Priority:** Must Have — blocking.
- **Estimasi:** 0.5–1 hari.
- **Status:** `Done`

## Acceptance Criteria

- [x] Laravel project baru terinisialisasi.
- [x] PostgreSQL lokal aktif dan dapat dikoneksikan (`.env` terkonfigurasi).
- [x] Redis lokal aktif dan dapat dikoneksikan (untuk cache & queue nanti).
- [x] Repository Git diinisialisasi dengan branch `main`; `.gitignore` bawaan Laravel diverifikasi mencakup `.env`.
- [x] Struktur folder kosong dibuat: `app/Domain/{Inbox,Tasks,Projects,Habits,KnowledgeBase,Notification,Review,Shared}/{Models,Actions,Enums,Events}`.
- [x] Laravel Pint terpasang dan dijalankan sekali untuk verifikasi (tidak ada file untuk diformat pada tahap ini, hanya memastikan tool terpasang).

## Checklist Sebelum Mulai

- [x] PHP versi sesuai requirement Laravel versi target sudah terpasang di mesin lokal.
- [x] Composer terpasang.

## Checklist Setelah Selesai

- [x] `php artisan serve` berjalan tanpa error.
- [x] Koneksi database dan Redis terverifikasi lewat `php artisan tinker` (atau setara).
- [x] Commit pertama dibuat: `chore: initial project setup`.
- [x] Pindahkan tiket ini ke `docs/tracking/DONE.md`, jadikan TASK-0002 sebagai `CURRENT_TASK.md` baru.
