# CORE_RULES.md

> **Aturan tertinggi.** Jika ada pertentangan antara file rules lain dan file ini, file ini yang menang. Dibaca wajib oleh AI di setiap sesi, sebelum `DEVELOPMENT_PLAYBOOK.md` dan `CURRENT_TASK.md` (urutan lengkap dan aturan mengikat AI ada di `/AI_INSTRUCTIONS.md`).

## 1. Jangan Mengulang Keputusan yang Sudah Final

Keputusan berikut **sudah final** dan tidak boleh diusulkan ulang tanpa permintaan eksplisit developer:
- Modular monolith Laravel (bukan microservices).
- PostgreSQL sebagai database.
- Tanpa Repository Pattern.
- Action Pattern untuk logika bisnis (bukan Service class besar generik).
- ULID sebagai primary key seluruh tabel.
- `user_id` di setiap tabel entitas utama sejak awal.

Lihat `docs/decisions/DECISIONS.md` untuk daftar lengkap dan alasannya.

## 2. Logika Sebelum UI

Setiap fitur ditulis mengikuti Coding Order (Implementation Guide bagian 5): Migration → Enum → Model → Factory → Policy → Form Request → Action (+unit test) → Event/Listener → Notification/Job → Livewire/UI → Feature Test → Seeder. **Tidak boleh** menulis Livewire component sebelum Action terkait lolos unit test.

## 3. Satu Sumber Kebenaran per Jenis Informasi

- Aturan bisnis per entitas → FSD & `docs/rules/DATABASE_RULES.md`.
- Keputusan teknis Laravel → TDD & `docs/rules/LARAVEL_RULES.md`/`ARCHITECTURE_RULES.md`.
- Urutan kerja → Implementation Guide & `docs/planning/ROADMAP.md`.

Jangan menyalin ulang isi dokumen acuan ke tempat lain secara verbatim — rujuk, jangan duplikasi (mencegah drift antar dokumen).

## 4. Setiap Perubahan terhadap Keputusan Final Harus Terdokumentasi

Jika implementasi menemukan bahwa satu keputusan dari dokumen acuan ternyata tidak dapat dijalankan persis, **jangan diam-diam menyimpang** — catat sebagai entri baru di `docs/decisions/DECISIONS.md` dengan alasan sebelum melanjutkan.

## 5. Isolasi Data per User Wajib di Setiap Query

Tidak ada satu query pun terhadap tabel entitas utama yang boleh berjalan tanpa filter `user_id` (lewat Policy atau global scope) — berlaku bahkan saat aplikasi masih single-user (lihat `docs/rules/SECURITY_RULES.md`).

## 6. Progres yang Dihitung Otomatis Tidak Boleh Bisa Diisi Manual

Progress bar Project/Goal (dan metrik agregat sejenis) **tidak pernah** disediakan sebagai input form — selalu hasil kalkulasi dari data sumber (Task selesai, dst).

## 7. Solo Developer adalah Reviewer Satu-Satunya

Tidak ada proses review dari pihak lain — Code Review Checklist (`docs/rules/DEVELOPMENT_RULES.md`) dijalankan sendiri sebelum setiap merge ke `main`. AI Assistant membantu menegakkan checklist ini secara konsisten setiap sesi.

## 8. Dokumentasi Diperbarui di Turn yang Sama dengan Kode

Menyelesaikan sebuah tiket tanpa memperbarui `CURRENT_TASK.md`/`DONE.md`/`CHANGELOG.md` di sesi yang sama dianggap **tiket belum selesai**, meski kodenya sudah berfungsi.
