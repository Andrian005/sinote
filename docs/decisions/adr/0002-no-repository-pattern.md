# ADR-0002: Tanpa Repository Pattern

- **Status:** Accepted
- **Tanggal:** Ditetapkan pada tahap Technical Architecture (TDD bagian 11)
- **Sumber terkait:** TDD bagian 11

## Context

Repository Pattern klasik dipakai untuk menyembunyikan detail ORM/database di balik interface, memudahkan penggantian ORM/database di masa depan. Perlu diputuskan apakah pola ini diterapkan di atas Eloquent.

## Decision

**Tidak menggunakan Repository Pattern.** Eloquent Model dipakai langsung di dalam Action class.

## Alternatives Considered

- **Repository Pattern penuh** — ditolak. Skenario mengganti ORM/database di masa depan sangat tidak mungkin terjadi (PostgreSQL + Eloquent sudah difinalkan di Blueprint v1.0). Menambahkan lapisan ini hanya berarti boilerplate ekstra yang harus dirawat solo developer tanpa manfaat nyata.

## Consequences

- **Positif:** lebih sedikit lapisan abstraksi untuk dirawat; Action class langsung berinteraksi dengan Eloquent yang sudah ekspresif.
- **Negatif/Trade-off:** jika suatu saat benar-benar perlu mengganti ORM (skenario yang dianggap sangat tidak mungkin), migrasi akan lebih menyebar di banyak Action dibanding jika ada satu lapisan Repository terpusat.

## Kapan Ditinjau Ulang

Praktis tidak akan ditinjau ulang kecuali ada perubahan fundamental pada pilihan database/ORM proyek — yang tidak diantisipasi terjadi.
