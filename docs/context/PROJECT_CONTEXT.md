# PROJECT_CONTEXT.md

> **Baca file ini pertama kali di setiap sesi kerja — manusia maupun AI.**

## Apa Proyek Ini

**Personal OS** adalah aplikasi web personal-productivity ("Personal Operating System" / "Second Brain") yang menyatukan siklus **Capture → Organize → Prioritize → Execute → Review** dalam satu sistem: menangkap ide, mengorganisasi ke Task/Project/Goal, memprioritaskan lewat Dashboard, mengeksekusi lewat Focus Mode, dan merefleksikan progres lewat Review berkala.

## Siapa Penggunanya

Single user (solo developer proyek ini adalah juga satu-satunya pengguna) — seorang kreator/pembelajar dengan banyak minat paralel (konten YouTube, desain, fotografi, pembelajaran bahasa & software). **Arsitektur dirancang siap tumbuh ke multi-user**, tapi scope fitur aktif tetap single-user sampai ada validasi kebutuhan nyata (lihat `docs/decisions/DECISIONS.md`).

## Batasan Utama yang Membentuk Seluruh Keputusan Teknis

- **Solo developer** — dikerjakan sendiri, di sela waktu (± 8–10 jam efektif/minggu).
- **Modular monolith berbasis Laravel** — bukan microservices (lihat ADR-0001).
- **PostgreSQL** sebagai database final (lihat ADR-0003).
- **Boring technology first** — pilih yang matang & terdokumentasi baik, bukan yang trendi.

## 12 Modul Fitur (dari FSD)

Inbox, Tasks, Projects & Goals, Tagging/Context, Dashboard, Deadline Reminder, Habit Tracking, Knowledge Base, Focus Mode, Review & Reflection, Full Notification Engine, Search — plus Core Infrastructure (Auth/User) sebagai fondasi non-modul.

## Dokumen Acuan (Source of Truth)

| Dokumen | Isi | Kapan Dibaca |
|---|---|---|
| Blueprint v1.0 | Visi produk, arsitektur tingkat tinggi, roadmap | Saat butuh konteks "kenapa" fitur ini ada |
| FSD | Spesifikasi fungsional lengkap per modul/fitur | Saat menulis Action/validasi untuk fitur tertentu |
| TDD | Keputusan implementasi teknis Laravel | Saat menulis struktur kode (Action, Event, Enum, dll) |
| Database & Business Rules Spec | Skema tabel & aturan bisnis per entitas | Saat menulis migration/Model |
| UI/UX Spec & Design System | Spesifikasi halaman & token desain | Saat menulis Livewire/Blade |
| Implementation Guide | Urutan eksekusi, sprint plan, checklist | Saat menentukan "apa selanjutnya" |

**Lokasi dokumen acuan:** disalin ke `docs/context/reference/` (lihat `PROJECT_OVERVIEW.md` untuk detail masing-masing versi ringkas).

## Yang TIDAK Boleh Diubah Tanpa Diskusi Eksplisit

- Struktur `user_id` di setiap tabel entitas utama.
- Keputusan "tanpa Repository Pattern" (ADR-0002).
- Pemisahan Deadline Reminder vs Full Notification Engine sebagai dua lapis pembangunan.
- Urutan Migration Order di Database Spec Bagian G.
- 6 item navigasi primer (Blueprint bagian 6) — Task sengaja tidak punya nav sendiri.
