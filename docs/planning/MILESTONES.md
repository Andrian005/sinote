# MILESTONES.md

*(Sumber: Implementation Guide bagian 7)*

## Milestone 1 — "Bisa Dipakai" (v0.1–v0.2)

- **Target:** Siklus Capture→Organize→Prioritize berjalan penuh.
- **Deliverable:** Core Infrastructure, Tagging, Inbox, Tasks, Projects & Goals, Dashboard, Deadline Reminder.
- **Kriteria Selesai:** Seluruh checklist "setelah selesai" tiap modul tercentang; dogfooding harian dimulai.
- **Risiko Utama:** Tergoda menambah fitur Habit/Note sebelum siklus inti stabil (scope creep).
- **Checklist:**
  - [ ] Auth berjalan
  - [ ] Tagging teruji
  - [ ] Inbox→Task konversi lancar
  - [ ] Progres Project akurat
  - [ ] Dashboard menampilkan data nyata
  - [ ] Reminder deadline terkirim

## Milestone 2 — "Habit & Fokus" (v0.3–v0.4)

- **Target:** Melampaui to-do list — kebiasaan dan sesi kerja terfokus.
- **Deliverable:** Habit Tracking, Knowledge Base, Focus Mode.
- **Kriteria Selesai:** Checklist modul 3.7–3.9 (Implementation Guide) tercentang penuh.
- **Risiko Utama:** Perhitungan streak salah tanpa disadari karena tidak diuji skenario retroaktif.
- **Checklist:**
  - [ ] Streak akurat
  - [ ] Note ter-link/ter-unlink dari Project dengan benar
  - [ ] Focus Mode dapat diakses dari 3 titik (Dashboard/Project/All-Tasks)

## Milestone 3 — "Reflektif" (v0.5)

- **Target:** Siklus penuh tertutup dengan ritual refleksi.
- **Deliverable:** Review & Reflection (Daily/Weekly/Monthly).
- **Kriteria Selesai:** Minimal satu siklus mingguan penuh sudah didogfooding.
- **Risiko Utama:** Snapshot metrik tidak benar-benar beku (bug paling kritis di modul ini).
- **Checklist:**
  - [ ] Snapshot tidak berubah setelah data sumber berubah
  - [ ] Reflection note auto-save berfungsi

## Milestone 4 — "Matang & Stabil" (v0.6–v1.0)

- **Target:** Notifikasi menyeluruh, pencarian, dan hardening produksi.
- **Deliverable:** Full Notification Engine, Search, Optimization, Deployment.
- **Kriteria Selesai:** Checklist di bawah + seluruh Development Checklist "Setelah Coding" + Deployment Guide.
- **Risiko Utama:** Menunda hardening keamanan/backup karena "terasa sudah selesai secara fitur".
- **Checklist:**
  - [ ] Reminder Habit/Review tidak duplikat
  - [ ] Search mengembalikan hasil akurat lintas modul
  - [ ] Backup otomatis + uji restore berhasil
  - [ ] CI/CD aktif
  - [ ] **v1.0 TIDAK dianggap rilis sah tanpa Security Strategy dan Backup Strategy aktif**

## Status Milestone Saat Ini

**Aktif:** Milestone 1, belum ada checklist yang tercentang — proyek baru memulai Sprint 1.
