# Personal OS — Developer Workspace

Workspace ini adalah fondasi kerja harian untuk membangun **Personal OS** di Visual Studio Code, dirancang agar AI Assistant (Claude Code, GitHub Copilot, atau agent lain) dan solo developer memiliki satu sumber konteks yang sama — tanpa perlu menjelaskan ulang latar belakang proyek di setiap sesi.

## Cara Memakai Workspace Ini

1. **Sebelum menulis kode apa pun** (manusia maupun AI), baca urutan berikut:
   `docs/context/PROJECT_CONTEXT.md` → `docs/rules/CORE_RULES.md` → `DEVELOPMENT_PLAYBOOK.md` → `docs/tracking/CURRENT_TASK.md` → `docs/decisions/DECISIONS.md`
2. Salin template dari `SESSION.md` untuk mencatat sesi kerja hari ini.
3. Kerjakan tiket yang sedang aktif di `docs/tracking/CURRENT_TASK.md` — detail tiketnya ada di folder `tickets/`.
4. Setelah tiket selesai, ikuti `DEVELOPMENT_PLAYBOOK.md` (dokumen utama proses development) untuk update dokumentasi, commit, dan menentukan tiket berikutnya. `WORKFLOW.md` berisi versi naratif yang sama; `AI_INSTRUCTIONS.md` berisi aturan mengikat khusus AI — lihat § "Sumber Kebenaran" di bawah untuk peran masing-masing.

## Struktur Folder

```
workspace/
├── README.md                  ← Anda di sini
├── DEVELOPMENT_PLAYBOOK.md    ← Dokumen UTAMA proses development (filosofi → workflow → DoD → checklist)
├── AI_INSTRUCTIONS.md          ← Aturan resmi & mengikat khusus AI Assistant
├── SESSION.md                  ← Template log sesi kerja harian
├── WORKFLOW.md                ← Development & AI Workflow (versi naratif, rujuk Playbook untuk detail)
├── docs/
│   ├── context/                ← Apa proyek ini, untuk siapa, kenapa ada
│   ├── rules/                  ← Aturan wajib (arsitektur, database, UI, Laravel, security, testing, git)
│   ├── coding/                 ← Standar penulisan kode & konvensi penamaan
│   ├── planning/                ← Roadmap, milestone, rencana rilis
│   ├── tracking/                ← Backlog, tugas aktif/berikutnya, selesai, blocker, bug, changelog
│   ├── decisions/               ← Log keputusan + Architecture Decision Records (ADR)
│   └── ai/                      ← Konteks & memori khusus untuk AI Assistant
└── tickets/
    ├── epics/                   ← EPIC — inisiatif besar setingkat modul
    ├── features/                ← FEATURE — fitur konkret dalam satu modul
    ├── tasks/                   ← TASK — unit kerja teknis terkecil
    ├── bugs/                    ← BUG — laporan & perbaikan cacat
    ├── chores/                  ← CHORE — pekerjaan pemeliharaan non-fitur
    └── refactors/                ← REFACTOR — perbaikan kualitas kode tanpa mengubah perilaku
```

## Sumber Kebenaran (Source of Truth)

Workspace ini **tidak menggantikan**, melainkan **menerjemahkan menjadi kerja harian**, enam dokumen acuan proyek yang sudah final:

1. Blueprint v1.0 (Product Discovery, Architecture, Roadmap)
2. Functional Specification Document (FSD)
3. Technical Design Document (TDD)
4. Database & Business Rules Specification
5. UI/UX Specification & Design System
6. Implementation Guide & Development Execution Plan

Jika ada pertentangan antara isi workspace ini dan salah satu dari 6 dokumen di atas, **dokumen aslinya yang menang** — file di workspace ini harus diperbarui untuk mencerminkan itu, dicatat di `docs/decisions/DECISIONS.md`.

## Hierarki Dokumen Workflow (Menghindari Kebingungan)

Ada tiga file di root yang tampak serupa — masing-masing punya peran berbeda, tidak saling menduplikasi:

| File | Peran |
|---|---|
| `DEVELOPMENT_PLAYBOOK.md` | **Dokumen utama** — filosofi, prinsip, dan seluruh siklus workflow (harian, tiket, dokumentasi, testing, review, rilis) dalam satu alur baca. Dibaca manusia **dan** AI. |
| `AI_INSTRUCTIONS.md` | Aturan **mengikat khusus AI** — versi ringkas-tegas dari Playbook, difokuskan pada apa yang boleh/tidak boleh dilakukan AI Assistant. |
| `WORKFLOW.md` | Versi naratif awal (masih valid, tidak dihapus) — penjelasan langkah-demi-langkah yang sama, cocok dibaca manusia yang ingin konteks lebih santai. Jika berbeda detail dengan Playbook, **Playbook yang menang**. |
