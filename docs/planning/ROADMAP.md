# ROADMAP.md

*(Sumber: Implementation Guide bagian 2, 6, 8 — dokumen ini adalah versi kerja yang diperbarui seiring progres; Implementation Guide asli tetap jadi rujukan penuh)*

## Development Order (Tingkat Tinggi)

```
1. Environment Setup
2. Core Infrastructure       (Auth, User, Enum dasar, Policy dasar)
3. Shared: Tagging/Context
4. Core Module: Inbox → Tasks → Projects & Goals
5. Supporting: Dashboard, Deadline Reminder
6. Advanced Tier 1: Habit Tracking, Knowledge Base, Focus Mode (fleksibel urutannya)
7. Advanced Tier 2: Review & Reflection → Full Notification Engine → Search
8. Optimization
9. Deployment (hardening v1.0)
```

## Sprint Map (20 Sprint Mingguan)

| Sprint | Fokus | Milestone |
|---|---|---|
| 1 | Core Infrastructure | M1 |
| 2 | Tagging/Context | M1 |
| 3–4 | Inbox + awal Tasks | M1 |
| 5 | Tasks lanjutan | M1 |
| 6–7 | Projects & Goals | M1 |
| 8 | Dashboard | M1 |
| 9 | Deadline Reminder | **M1 selesai** |
| 10–11 | Habit Tracking | M2 |
| 12 | Knowledge Base | M2 |
| 13 | Focus Mode | **M2 selesai** |
| 14–15 | Review & Reflection | **M3 selesai** |
| 16–17 | Full Notification Engine | M4 |
| 18 | Search | M4 |
| 19 | Optimization | M4 |
| 20 | Deployment Hardening | **M4 selesai → v1.0** |

## Status Saat Ini

**Sprint aktif:** Sprint 3 — Inbox / Capture (EPIC-002). 

**Sprint selesai:**
- ✅ Sprint 1 — Core Infrastructure (EPIC-000) — selesai 2026-07-25
- ✅ Sprint 2 — Tagging/Context (EPIC-001) — selesai 2026-07-26

Lihat `docs/tracking/CURRENT_TASK.md` untuk tiket spesifik yang sedang berjalan.

## Aturan Update Dokumen Ini

Perbarui tabel Sprint Map di atas (kolom implisit "status") lewat `docs/tracking/DONE.md` — jangan menghapus baris sprint yang sudah selesai dari tabel ini, cukup rujuk status penyelesaiannya di `DONE.md` agar riwayat roadmap tetap utuh.
