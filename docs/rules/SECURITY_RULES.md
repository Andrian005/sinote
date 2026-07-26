# SECURITY_RULES.md

*(Ringkasan operasional dari TDD bagian 18–20, 26–27 dan Blueprint bagian 20)*

## Wajib di Setiap Environment

- HTTPS penuh (kecuali `local`).
- Kredensial seluruhnya di `.env`, tidak pernah hardcode/ter-commit (`.gitignore` bawaan Laravel tidak dimodifikasi untuk mengizinkan `.env`).
- Password hashing via driver bawaan Laravel — tidak membuat mekanisme hashing sendiri.
- CSRF protection bawaan **tidak** dinonaktifkan di form manapun.

## Autentikasi & Otorisasi

- Session-based auth (Breeze/Fortify) sebagai jalur utama web.
- Sanctum disiapkan berdampingan untuk API masa depan — token Sanctum **tidak** dipakai di alur web utama.
- Setiap entitas utama punya Policy; Policy dicek di titik masuk Controller/Livewire sebelum Action dijalankan.
- Isolasi data per user wajib di setiap query (`CORE_RULES.md` § 5).

## Rate Limiting

- Login: maksimal 5 percobaan/menit per kombinasi email+IP.
- Quick Capture: rate limit longgar (~60 request/menit) — cukup mencegah bug infinite-loop, tidak mengganggu pemakaian wajar.
- API (saat aktif): mengikuti default `throttle:api` per token Sanctum.

## Mass Assignment

- `$fillable` eksplisit di setiap Model — dilarang `$guarded = []`.

## Logging Keamanan

- Channel log `security` terpisah untuk percobaan login gagal dan perubahan kepemilikan data — ditinjau setiap sesi maintenance (lihat `docs/rules/TESTING_RULES.md` dan maintenance guide di Implementation Guide bagian 14).

## Checklist Sebelum Deploy ke Production

- [ ] `.env` production terisi lengkap, tidak ada default development tertinggal.
- [ ] HTTPS aktif.
- [ ] Rate limiting login aktif dan teruji.
- [ ] Tidak ada credential di riwayat Git (`git log -p` spot-check sebelum deploy pertama).
- [ ] Backup otomatis aktif sejak deploy pertama (bukan ditambahkan belakangan) — lihat `docs/planning/RELEASE_PLAN.md`.
