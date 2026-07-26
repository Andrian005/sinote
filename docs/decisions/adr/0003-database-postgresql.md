# ADR-0003: PostgreSQL sebagai Database Final

- **Status:** Accepted
- **Tanggal:** Ditetapkan pada audit Blueprint v1.0 (menggantikan rekomendasi "MySQL atau PostgreSQL" di TDD awal)
- **Sumber terkait:** Blueprint v1.0 Catatan Audit #5, TDD bagian 25

## Context

Technical Design Document awal merekomendasikan "MySQL atau PostgreSQL" tanpa keputusan final — audit Blueprint v1.0 mengidentifikasi ini sebagai keputusan teknis yang menggantung dan berisiko menunda dimulainya development.

## Decision

**PostgreSQL** ditetapkan sebagai database final.

## Alternatives Considered

- **MySQL** — tidak dipilih. Meski sama-sama matang dan didukung penuh Laravel, PostgreSQL diunggulkan untuk fitur data lanjutan (tipe `jsonb` dengan index GIN, full-text search native) yang relevan untuk Future Enhancement analitik tren (`review_entries.snapshot_metrics`) dan pencarian lintas modul di masa depan.

## Consequences

- **Positif:** `jsonb` dipakai untuk `review_entries.snapshot_metrics` dengan potensi index GIN; jalur upgrade ke full-text search native tersedia tanpa mengganti database di kemudian hari.
- **Negatif/Trade-off:** tidak ada trade-off signifikan — PostgreSQL dan MySQL setara dalam dukungan Laravel dan biaya hosting managed.

## Kapan Ditinjau Ulang

Tidak diantisipasi ditinjau ulang — ini adalah keputusan final sesuai audit Blueprint v1.0.
