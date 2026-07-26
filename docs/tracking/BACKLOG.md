# BACKLOG.md

> Daftar seluruh EPIC dan FEATURE yang belum dikerjakan, diurutkan sesuai Dependency Map (`docs/planning/ROADMAP.md`). Tiket detail ada di folder `tickets/`. Saat sebuah EPIC/FEATURE mulai dikerjakan, pindahkan ke `docs/tracking/CURRENT_TASK.md`; saat selesai, pindahkan ke `docs/tracking/DONE.md`.

## Urutan Pengerjaan (Tidak Boleh Dibalik)

- [ ] **EPIC-000** — Core Infrastructure *(lihat `tickets/epics/EPIC-000-core-infrastructure.md`)*
- [ ] **EPIC-001** — Tagging & Context *(`tickets/epics/EPIC-001-tagging-context.md`)*
- [ ] **EPIC-002** — Inbox / Capture *(`tickets/epics/EPIC-002-inbox.md`)*
- [ ] **EPIC-003** — Tasks *(`tickets/epics/EPIC-003-tasks.md`)*
- [ ] **EPIC-004** — Projects & Goals *(`tickets/epics/EPIC-004-projects-goals.md`)*
- [ ] **EPIC-005** — Dashboard / Today View *(`tickets/epics/EPIC-005-dashboard.md`)*
- [ ] **EPIC-006** — Deadline Reminder *(`tickets/epics/EPIC-006-deadline-reminder.md`)*

## Dapat Dikerjakan Paralel/Fleksibel Setelah EPIC-000 & EPIC-001 (lihat Dependency Map)

- [ ] **EPIC-007** — Habit Tracking *(`tickets/epics/EPIC-007-habit-tracking.md`)*
- [ ] **EPIC-008** — Knowledge Base *(`tickets/epics/EPIC-008-knowledge-base.md`)*
- [ ] **EPIC-009** — Focus Mode *(`tickets/epics/EPIC-009-focus-mode.md`)*

## Tier 2 — Menunggu Tier Sebelumnya Selesai

- [ ] **EPIC-010** — Review & Reflection *(`tickets/epics/EPIC-010-review-reflection.md`)*
- [ ] **EPIC-011** — Full Notification Engine *(`tickets/epics/EPIC-011-full-notification-engine.md`)*
- [ ] **EPIC-012** — Search *(`tickets/epics/EPIC-012-search.md`)*

## Penutup Menuju v1.0

- [ ] **EPIC-013** — Optimization *(`tickets/epics/EPIC-013-optimization.md`)*
- [ ] **EPIC-014** — Deployment Hardening *(`tickets/epics/EPIC-014-deployment-hardening.md`)*

## Cara Memecah EPIC Menjadi FEATURE/TASK

Setiap EPIC di atas berisi daftar FEATURE yang perlu dipecah lebih lanjut menjadi TASK saat EPIC tersebut **akan dimulai** (bukan semua dipecah di muka) — mengikuti Sprint Planning di Implementation Guide bagian 8. Lihat `tickets/features/` dan `tickets/tasks/` untuk contoh yang sudah dipecah (EPIC-000, mewakili Sprint 1) sebagai template pemecahan untuk EPIC berikutnya.

**Jangan memecah seluruh backlog di muka** — ini bertentangan dengan prinsip iteratif Blueprint (menghindari over-engineering perencanaan). Pecah satu EPIC menjadi FEATURE/TASK saat sprint untuk EPIC tersebut akan dimulai, mengikuti sprint map di `docs/planning/ROADMAP.md`.
