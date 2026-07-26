# DEVELOPMENT_RULES.md

*(Menerjemahkan Implementation Guide bagian 9 & 11 menjadi aturan operasional harian)*

## Sebelum Coding

- [ ] Environment lokal (Laravel, PostgreSQL, Redis) aktif dan diverifikasi.
- [ ] `docs/tracking/CURRENT_TASK.md` menunjuk ke satu tiket yang jelas (bukan kosong/ambigu).
- [ ] Tiket terkait di `tickets/` sudah dibaca lengkap (Acceptance Criteria, Dependency).

## Saat Coding

- [ ] Mengikuti Coding Order (`CORE_RULES.md` § 2) untuk setiap tiket.
- [ ] Laravel Pint dijalankan sebelum setiap commit.
- [ ] Unit test ditulis untuk setiap Action berisiko tinggi **sebelum** disambungkan ke Livewire.
- [ ] Nama file/class mengikuti `docs/coding/NAMING_CONVENTION.md` tanpa pengecualian.
- [ ] Tidak menambah dependency/package baru tanpa mencatat alasannya di `docs/decisions/DECISIONS.md`.

## Code Review Checklist (dijalankan sendiri sebelum merge ke `main`)

- [ ] **Naming Convention** — sesuai `docs/coding/NAMING_CONVENTION.md`.
- [ ] **Clean Code** — satu Action = satu tanggung jawab; tidak ada method >30–40 baris tanpa alasan kuat.
- [ ] **SOLID** — Single Responsibility (Action), Dependency Inversion (dependency lewat constructor/parameter).
- [ ] **Performance** — eager loading diterapkan; tidak ada query dalam loop.
- [ ] **Security** — `$fillable` eksplisit; Policy dipanggil sebelum Action; input tervalidasi.
- [ ] **Validation** — seluruh input lewat Form Request sesuai FSD.
- [ ] **Error Handling** — Exception domain spesifik untuk kondisi bisnis yang sudah teridentifikasi.
- [ ] **Logging** — Event penting lintas modul tercatat di channel `jobs`.
- [ ] **Documentation** — nama Action/Event self-documenting.

## Setelah Coding

- [ ] Refactoring ringan jika satu Action/Model mulai menyerap tanggung jawab modul lain.
- [ ] Bug dari dogfooding dicatat di `docs/tracking/BUGS.md` dan diperbaiki sebelum modul berikutnya dimulai.
- [ ] Query log ditinjau manual untuk mendeteksi N+1.
- [ ] Security review dasar dicek ulang per modul.

## Definition of Done (Berlaku untuk Semua Tiket)

Sebuah tiket **hanya** dianggap selesai jika:
1. Acceptance Criteria di file tiket tercentang penuh.
2. Unit test (jika berlaku untuk tiket ini) hijau.
3. Feature test (jika berlaku) hijau.
4. Code Review Checklist di atas sudah dijalankan.
5. `docs/tracking/DONE.md` dan `docs/tracking/CHANGELOG.md` sudah diperbarui.
6. File tiket di `tickets/` sudah diubah status menjadi `Done` dengan checklist tercentang.
