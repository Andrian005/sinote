# BEST_PRACTICES.md

*(Kumpulan praktik yang harus diikuti berulang kali di seluruh modul — kompilasi dari TDD, Database Spec, dan Implementation Guide)*

## Query & Performance

- Selalu `select()` eksplisit untuk kolom yang benar-benar dibutuhkan (hindari `select *`).
- Selalu `with()` eager loading untuk relasi yang pasti diakses bersamaan (mis. Task→Project→Goal di Dashboard).
- Periksa query log manual setiap kali membangun halaman baru yang menampilkan list/agregasi — deteksi N+1 sebelum tiket dianggap selesai.

## Menulis Action Baru

1. Cek dulu apakah operasi ini benar-benar butuh Action baru, atau cukup accessor/mutator ringan di Model (jika logika murni milik satu entitas, tidak lintas-entitas).
2. Tulis unit test **sebelum** menyambungkan ke Livewire.
3. Jika Action ini memicu efek di modul lain, dispatch Event — jangan panggil Action modul lain secara langsung.
4. Tulis nama Action sejelas mungkin sehingga niat operasi terbaca tanpa membuka isi file.

## Menangani Relasi Opsional (Nullable Foreign Key)

Banyak relasi di skema ini sengaja nullable (Task↔Project, Project↔Goal, Note↔Project) untuk menjaga fleksibilitas struktur data. Saat menulis Factory atau seed data, **jangan** selalu mengasumsikan relasi lengkap ada — uji juga skenario entitas berdiri bebas (tanpa induk).

## Menangani Status & Transisi

- Sebelum menulis Action yang mengubah status, buka dulu State Machine Diagram entitas terkait di Database Spec Bagian C — transisi yang tidak digambarkan di situ dianggap tidak valid dan harus ditolak (via Exception domain spesifik).

## Menulis Reminder/Notification Baru

- Selalu cek ulang kondisi tepat sebelum mengirim (bukan hanya di awal job scanner) — mencegah reminder terkirim untuk entitas yang statusnya sudah berubah sejak job dimulai.
- Reminder yang sudah tidak relevan (entitas selesai/dihapus) harus dibatalkan (`status = cancelled`), bukan dibiarkan `scheduled` selamanya.

## Menulis Halaman Baru (Livewire + Blade)

- Cek `docs/rules/UI_RULES.md` dan spesifikasi halaman terkait di UI/UX Spec sebelum menulis satu baris Blade.
- Skeleton loading per komponen, bukan spinner blocking seluruh halaman.
- Empty state selalu spesifik konteks, bukan pesan generik.

## Menghindari Over-Engineering

Jika ragu apakah suatu abstraksi (Repository, Service class besar, Value Object) dibutuhkan — jawabannya biasanya **tidak**, kecuali FSD/TDD secara eksplisit menyebutkan kebutuhan tersebut. Default ke pendekatan paling sederhana yang sudah terbukti cukup di dokumen acuan.
