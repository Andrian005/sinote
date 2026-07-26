# EPIC-003: Tasks

- **ID:** EPIC-003
- **Judul:** Tasks — Unit Eksekusi Harian
- **Deskripsi:** CRUD Task, State Machine (todo/in_progress/done/archived), dan Event `TaskCompleted` (FSD Modul 2).
- **Dependency:** EPIC-000, EPIC-001, EPIC-002 (sebagai sumber konversi).
- **Priority:** Must Have — MVP 0/1.
- **Estimasi:** 4–6 hari.
- **Status:** `Backlog`

## Acceptance Criteria

- [ ] CRUD Task lengkap (judul, deskripsi, prioritas, deadline, Project opsional, tag).
- [ ] Transisi status sesuai State Machine di Database Spec Bagian B.1 — transisi tidak valid (mis. `archived→done` langsung) ditolak.
- [ ] Task dapat berdiri bebas tanpa Project (relasi opsional teruji).
- [ ] Event `TaskCompleted` di-dispatch saat status → `done`.
- [ ] Reopen (`done→todo`) berfungsi.

## Checklist Sebelum Mulai

- [ ] EPIC-002 selesai penuh, termasuk unit test konversi Inbox→Task.

## Checklist Setelah Selesai

- [ ] Unit test State Machine hijau.
- [ ] `docs/tracking/DONE.md` diperbarui.
- [ ] EPIC-004 (Projects & Goals) siap dimulai.

## Feature/Task Turunan

- FEAT — Task Creation & Editing (FSD 2.1)
- FEAT — Task Completion & Status Management (FSD 2.2)
