# PROJECT_OVERVIEW.md

## Ringkasan Status Proyek

| Aspek | Status |
|---|---|
| Fase saat ini | Pre-implementation — seluruh dokumentasi desain selesai, workspace baru dibuat |
| Versi target berikutnya | v0.1 (MVP 0 — Skeleton) |
| Milestone aktif | Milestone 1 — "Bisa Dipakai" |
| Sprint aktif | Sprint 1 — Core Infrastructure |

## Ringkasan 6 Dokumen Acuan

1. **Blueprint v1.0** — Konsolidasi & audit dari Tahap 1–4 (Discovery, Feature Planning, Technical Architecture, Development Planning). Memutuskan: Tagging/Context = Must Have, Reminder dipecah 2 lapis, Focus Mode = lapisan UI (bukan entitas data), Database = PostgreSQL final.
2. **FSD** — Spesifikasi 28-dimensi (tujuan, workflow, business rules, state machine, dst.) untuk 12 modul fitur.
3. **TDD** — 38 keputusan teknis Laravel: modular monolith, Action Pattern (bukan Service class besar), tanpa Repository Pattern, Enum untuk status, Event/Listener mapping, cache-aside dengan invalidasi Event untuk Dashboard.
4. **Database & Business Rules Spec** — 12 tabel dengan ULID PK, soft delete pada entitas utama, cascade rules asimetris (set-null untuk entitas bermakna sendiri, cascade untuk log murni), business rules per entitas.
5. **UI/UX Spec & Design System** — Spesifikasi 9 halaman utama + design system Tailwind (color, typography, component rules, dark mode direkomendasikan sejak awal).
6. **Implementation Guide** — Urutan eksekusi: Core Infra → Tagging → Inbox → Tasks → Projects&Goals → Dashboard+Reminder → (Habit/Notes/Focus paralel) → Review → Full Notification → Search → Optimization → Deployment. 20 sprint mingguan, 4 milestone.

## Salinan Dokumen Acuan

Salin (atau symlink) keenam dokumen markdown asli ke `docs/context/reference/` dengan nama:
```
docs/context/reference/01-blueprint-v1.md
docs/context/reference/02-fsd.md
docs/context/reference/03-tdd.md
docs/context/reference/04-database-business-rules.md
docs/context/reference/05-uiux-design-system.md
docs/context/reference/06-implementation-guide.md
```
Workspace ini merujuk ke keenamnya lewat nama file di atas — jika belum disalin, salin terlebih dahulu sebelum memulai Sprint 1.

## Bagaimana Dokumen Ini Berbeda dari PRODUCT_VISION.md dan PROJECT_CONTEXT.md

- `PROJECT_CONTEXT.md` = jawaban cepat "apa, siapa, batasan apa" — dibaca di **setiap** sesi.
- `PROJECT_OVERVIEW.md` (dokumen ini) = peta status proyek & ringkasan tiap dokumen acuan — dibaca saat **orientasi ulang** (mis. setelah jeda panjang tidak menyentuh proyek).
- `PRODUCT_VISION.md` = visi & misi produk jangka panjang — dibaca saat mengambil keputusan yang berdampak ke arah produk.
