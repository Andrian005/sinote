# CHORE-0001: [Contoh Template] Update Dependency Bulanan

> **Ini adalah tiket contoh/template** untuk pekerjaan pemeliharaan non-fitur berulang. Salin struktur ini setiap sesi maintenance bulanan (lihat Implementation Guide bagian 14 / `WORKFLOW.md`).

- **ID:** CHORE-0001
- **Judul:** Update dependency bulanan (Composer & NPM)
- **Deskripsi:** Meninjau `composer outdated`/`npm outdated`, memperbarui dependency non-major secara rutin, mencatat dependency major yang tertunda untuk direncanakan terpisah.
- **Modul Terdampak:** Seluruh aplikasi (cross-cutting, bukan modul fitur spesifik).
- **Dependency:** Tidak ada.
- **Priority:** Rendah–Sedang (rutin, dijadwalkan bulanan — naik prioritas jika ada CVE diketahui).
- **Estimasi:** 1–2 jam.
- **Status:** `Backlog` *(contoh — jadwalkan ulang setiap bulan)*

## Acceptance Criteria

- [ ] `composer outdated` dan `npm outdated` dijalankan dan hasilnya ditinjau.
- [ ] Dependency non-major diperbarui, test suite dijalankan penuh setelahnya — hijau.
- [ ] Dependency major yang tertunda dicatat sebagai CHORE terpisah dengan estimasi upaya migrasi.
- [ ] `composer audit` dijalankan, vulnerability yang ditemukan ditindaklanjuti sesuai urgensinya (CVE tinggi → segera, bukan menunggu jadwal bulanan berikutnya).

## Checklist

- [ ] Tidak ada breaking change tak terduga (test suite hijau setelah update).
- [ ] `docs/tracking/CHANGELOG.md` dicatat satu baris ringkas.
- [ ] Jika ditemukan CVE tingkat tinggi, dicatat juga di `docs/rules/SECURITY_RULES.md` sebagai catatan tindak lanjut jika relevan jangka panjang.

## Catatan

Chore lain yang mengikuti pola serupa: pembersihan `AI_MEMORY.md` dari entri usang, peninjauan index database yang jarang terpakai (lihat Implementation Guide bagian 14 § Database Maintenance), verifikasi restore backup bulanan.
