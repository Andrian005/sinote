# CODING_STANDARDS.md

*(Ringkasan operasional dari TDD bagian 32)*

## Standar Dasar

- **PSR-12**, ditegakkan otomatis lewat **Laravel Pint** — jangan berdebat gaya kode secara manual, biarkan Pint yang memutuskan.
- Tidak ada linter/formatter custom tambahan di luar Pint — menghindari alat yang perlu dirawat terpisah oleh solo developer.

## Prinsip Clean Code yang Ditegakkan

1. **Single Responsibility** — satu Action class = satu operasi bisnis. Jika sebuah Action mulai melakukan lebih dari satu hal yang tidak berkaitan erat, pecah menjadi dua Action.
2. **Self-documenting code di atas komentar** — nama Action/Event/Method harus cukup deskriptif sehingga jarang butuh komentar penjelas. Komentar dipakai untuk **alasan** (kenapa), bukan **apa** (yang sudah jelas dari kode).
3. **Tidak ada "fat model" / "fat controller"** — logika lintas-entitas selalu di Action (lihat `ARCHITECTURE_RULES.md`).
4. **Dependency lewat constructor/parameter**, bukan `new` di dalam method — memudahkan testing dan mengikuti Dependency Inversion.
5. **Tidak ada magic number/string** — status memakai Enum, konstanta memakai `const`/config, bukan literal tersebar di kode.

## Batas Ukuran (Panduan, Bukan Aturan Kaku)

- Method Action: idealnya <30–40 baris. Jika lebih, pertimbangkan apakah Action ini melakukan lebih dari satu tanggung jawab.
- Livewire Component: logika bisnis **tidak** boleh ada di sini sama sekali — hanya memanggil Action dan mengatur state tampilan.

## Contoh Pola yang Diharapkan (Deskriptif, Bukan Kode)

Sebuah Action yang baik: menerima input eksplisit → memvalidasi prasyarat bisnis (jika ada, lempar Exception domain spesifik) → melakukan satu perubahan data → men-dispatch Event jika ada efek lintas modul → mengembalikan hasil. Tidak mencampur pengambilan keputusan otorisasi (itu tugas Policy) atau validasi format input (itu tugas Form Request).

## Checklist Gaya Kode

- [ ] `php artisan pint` bersih tanpa perubahan tersisa.
- [ ] Tidak ada method Action yang melakukan lebih dari satu tanggung jawab jelas.
- [ ] Tidak ada magic string untuk status — semua lewat Enum.
