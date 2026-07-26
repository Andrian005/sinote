# BUG-0001: [Contoh Template] Streak Habit Salah Hitung Setelah Frekuensi Diubah

> **Ini adalah tiket contoh/template** — menunjukkan format yang wajib diikuti setiap kali bug baru ditemukan (termasuk dari dogfooding). Salin struktur ini, ganti isinya, beri ID `BUG-000X` berikutnya secara berurutan.

- **ID:** BUG-0001
- **Judul:** Streak Habit salah hitung setelah frekuensi diubah di tengah riwayat
- **Deskripsi:** *(Contoh)* Saat frekuensi Habit diubah dari `daily` ke `n_per_week`, streak yang ditampilkan menghitung ulang seluruh riwayat lama dengan aturan baru — seharusnya streak lama tetap dihitung dengan aturan lama (lihat FSD 7.1 Business Rule, Database Spec B.4).
- **Modul Terdampak:** Habit Tracking (EPIC-007).
- **Severity:** Medium (perilaku menyimpang dari spesifikasi, tidak menghalangi pemakaian core).
- **Dependency:** Tidak ada — perbaikan berdiri sendiri.
- **Priority:** Sedang (dijadwalkan sebelum EPIC-010 dimulai, karena Review membutuhkan data streak yang akurat).
- **Estimasi:** 0.5–1 hari.
- **Status:** `Backlog` *(contoh)*

## Langkah Reproduksi

1. Buat Habit dengan `frequency_type = daily`, check-in 5 hari berturut-turut.
2. Ubah `frequency_type` menjadi `n_per_week` dengan target 3.
3. Amati nilai streak yang ditampilkan.

## Perilaku Saat Ini (Salah)

Streak dihitung ulang dari awal menggunakan aturan `n_per_week`, mengubah riwayat 5 hari yang tercatat dengan aturan `daily`.

## Perilaku yang Diharapkan

Streak dari periode `daily` tetap dihitung dengan aturan `daily` (5 hari berturut-turut tetap tercatat apa adanya); hanya periode setelah perubahan yang memakai aturan `n_per_week`.

## Acceptance Criteria (Perbaikan Dianggap Selesai Jika)

- [ ] Unit test ditulis untuk skenario ini (mengubah frekuensi di tengah riwayat), gagal terhadap kode saat ini.
- [ ] `RecalculateHabitStreakAction` diperbaiki sehingga test di atas hijau.
- [ ] Regresi: seluruh unit test Habit lain yang sudah ada tetap hijau setelah perbaikan.

## Checklist

- [ ] Root cause diidentifikasi terhadap FSD/Database Spec (bukan sekadar menambal gejala).
- [ ] Test reproduksi ditulis sebelum perbaikan (test-first untuk bug).
- [ ] Fix di-review lewat Code Review Checklist (`docs/rules/DEVELOPMENT_RULES.md`).
- [ ] `docs/tracking/BUGS.md` diperbarui statusnya, `CHANGELOG.md` ditambahkan satu baris.
