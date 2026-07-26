# RELEASE_PLAN.md

*(Sumber: Implementation Guide bagian 13, TDD bagian 37–38, Blueprint bagian 24)*

## Versi & Rilis

| Versi | Fokus | Milestone |
|---|---|---|
| v0.1 | MVP 0 — Skeleton (Auth, Inbox, Task, Dashboard sederhana) | M1 |
| v0.2 | MVP 1 — Projects & Goals, Deadline Reminder, Tagging | M1 |
| v0.3 | Habit Tracking | M2 |
| v0.4 | Knowledge Base + Focus Mode | M2 |
| v0.5 | Review & Reflection | M3 |
| v0.6 | Search + Full Notification Engine | M4 |
| v1.0 | Stabilisasi, hardening keamanan/performa/backup | M4 |

## Environment

`local` (development harian) → `production` langsung. **Staging ditunda** untuk solo developer — perubahan diuji memadai secara lokal + test suite sebelum deploy.

## Checklist Sebelum Deploy Pertama ke Production

- [ ] **Environment Variable**: seluruh `.env` production terisi benar (DB, Redis, storage, mail).
- [ ] **Migration**: `php artisan migrate --force` sebagai bagian langkah deploy otomatis.
- [ ] **Seeder**: hanya seeder produksi yang dijalankan (bukan seeder data uji).
- [ ] **Queue**: Horizon aktif, disupervisi (Supervisor/systemd) untuk restart otomatis.
- [ ] **Cache**: Redis cache di-clear pasca-deploy jika ada perubahan struktur data.
- [ ] **Scheduler**: cron `* * * * * php artisan schedule:run` aktif di server.
- [ ] **Backup**: `spatie/laravel-backup` terjadwal aktif sejak deploy pertama.
- [ ] **Monitoring**: Sentry terpasang sebelum trafik harian nyata dimulai.

## CI/CD

- **CI:** GitHub Actions (atau setara) — Pint, test suite Pest, `composer audit` — wajib lolos sebelum merge ke `main`.
- **CD:** deployment otomatis setelah merge ke `main` lolos CI, zero-downtime (mis. Laravel Forge deploy script).

## Riwayat Rilis

*(Diperbarui setiap kali versi baru benar-benar dirilis — lihat juga `docs/tracking/CHANGELOG.md` untuk detail per perubahan)*

| Versi | Tanggal Rilis | Catatan |
|---|---|---|
| — | — | Belum ada rilis — proyek pada fase pre-implementation |
