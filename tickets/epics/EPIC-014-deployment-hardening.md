# EPIC-014: Deployment Hardening

- **ID:** EPIC-014
- **Judul:** Deployment Hardening — Menuju Rilis v1.0
- **Deskripsi:** Hardening keamanan, backup, CI/CD, dan monitoring sebagai penutup sebelum v1.0 dianggap layak dirilis (TDD bagian 26–30, 37–38; Blueprint bagian 24; Implementation Guide bagian 13).
- **Dependency:** EPIC-013 (Optimization).
- **Priority:** Must Have — blocking rilis v1.0.
- **Estimasi:** ~1 minggu.
- **Status:** `Backlog`

## Acceptance Criteria

- [ ] CI aktif: Laravel Pint, test suite Pest, `composer audit` wajib lolos sebelum merge ke `main`.
- [ ] CD zero-downtime terpasang (mis. Laravel Forge deploy script atau setara).
- [ ] `.env` production terisi lengkap dan diverifikasi (tidak ada default development tertinggal).
- [ ] Backup otomatis (`spatie/laravel-backup`) aktif, retensi harian/mingguan/bulanan, **dan sudah diuji restore minimal satu kali**.
- [ ] Sentry (atau setara) terpasang sebelum trafik harian nyata dimulai.
- [ ] Scheduler (`php artisan schedule:run` via cron) aktif dan terverifikasi menjalankan seluruh Job reminder/Review.
- [ ] Rate limiting login dan Quick Capture aktif sesuai `docs/rules/SECURITY_RULES.md`.

## Checklist Sebelum Mulai

- [ ] EPIC-013 selesai.
- [ ] Seluruh EPIC-005 s/d EPIC-012 sudah didogfooding minimal beberapa hari tanpa bug Critical/High terbuka (lihat `docs/tracking/BUGS.md`).

## Checklist Setelah Selesai

- [ ] **v1.0 resmi dirilis** — catat di `docs/planning/RELEASE_PLAN.md` § Riwayat Rilis.
- [ ] `docs/tracking/DONE.md` diperbarui.
- [ ] Milestone 4 ("Matang & Stabil") selesai.

## Feature/Task Turunan

*(Dipecah menjadi TASK teknis spesifik saat EPIC ini dimulai — checklist Acceptance Criteria di atas sudah cukup granular untuk langsung dijadikan TASK satu-per-satu.)*
