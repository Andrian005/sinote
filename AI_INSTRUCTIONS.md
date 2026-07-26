# AI_INSTRUCTIONS.md

> **Aturan resmi dan mengikat bagi AI Assistant** (Claude Code, GitHub Copilot, atau agent lain) yang bekerja di repository ini. Ini adalah sumber kebenaran tunggal untuk perilaku AI — `docs/ai/AI_CONTEXT.md` melengkapi dengan penjelasan peran/nuansa, `DEVELOPMENT_PLAYBOOK.md` § 4 hanya merujuk balik ke sini. Jika ada pertentangan antar ketiganya, **file ini yang menang**.

## Sebelum Coding, AI Wajib Membaca (Urutan Tetap)

1. `docs/context/PROJECT_CONTEXT.md` — apa proyek ini, batasannya.
2. `docs/rules/CORE_RULES.md` — aturan tertinggi yang tidak boleh dilanggar.
3. `DEVELOPMENT_PLAYBOOK.md` — filosofi, prinsip, dan seluruh workflow proyek.
4. `docs/tracking/CURRENT_TASK.md` — tiket apa yang sedang aktif.
5. `docs/decisions/DECISIONS.md` — keputusan apa saja yang sudah final.

Tidak ada pengecualian — bahkan untuk perubahan yang tampak trivial (typo, styling kecil), keempat file pertama tetap wajib dibaca agar AI tidak kehilangan konteks proyek secara keseluruhan.

## AI Wajib

1. **Mengikuti seluruh dokumentasi** — `docs/rules/*.md` yang relevan dengan pekerjaan saat ini (mis. `DATABASE_RULES.md` untuk migration, `UI_RULES.md` untuk Livewire) dibaca sebelum menulis kode di area tersebut.
2. **Tidak mengubah arsitektur tanpa alasan** — keputusan di `docs/decisions/DECISIONS.md` dan ADR (`docs/decisions/adr/`) bersifat final. Jika AI menganggap ada pendekatan lebih baik, sampaikan sebagai catatan/pertanyaan ke developer — jangan langsung mengimplementasikan penyimpangan.
3. **Tidak mengerjakan fitur di luar scope tiket aktif** — jika saat mengerjakan TASK-X, AI menemukan peluang perbaikan/fitur di modul lain, catat sebagai tiket baru (BUG/CHORE/REFACTOR sesuai jenisnya) di folder `tickets/`, **jangan** dikerjakan langsung di luar scope.
4. **Menjelaskan alasan keputusan teknis** — setiap kali AI memilih satu dari beberapa pendekatan yang mungkin (mis. struktur query, nama variabel non-trivial, pendekatan test), sertakan alasan singkat dalam respons ke developer, bukan hanya menyajikan kode jadi.
5. **Menjaga konsistensi proyek** — namespace, penamaan, dan pola (Action/Event/Enum) mengikuti persis `docs/coding/NAMING_CONVENTION.md` dan `docs/rules/ARCHITECTURE_RULES.md`, tanpa variasi gaya pribadi.
6. **Memperbarui dokumentasi jika diperlukan** — lihat § "Setelah Tiket Selesai" di bawah.
7. **Mengikuti Coding Order** (`docs/rules/CORE_RULES.md` § 2 / `DEVELOPMENT_PLAYBOOK.md` § 5) — logika dan unit test sebelum UI, tanpa kecuali, tanpa alasan "demi kecepatan".
8. **Menegakkan Definition of Done** (`DEVELOPMENT_PLAYBOOK.md` § 12) sebelum menyatakan sebuah tiket selesai ke developer.

## AI Dilarang

- Membuat Repository class, Service class besar generik, atau Value Object untuk primitif sederhana.
- Menulis Livewire/Blade sebelum Action terkait memiliki unit test hijau.
- Melompati urutan Dependency Map (`docs/planning/ROADMAP.md`) meski "secara teknis lebih cepat" mengerjakan modul lain lebih dulu.
- Membuat progress bar/metrik agregat menjadi input form yang dapat diisi manual.
- Mengasumsikan aplikasi multi-user secara fungsional — tetap single-user meski struktur data siap untuk itu.
- Menghapus atau menimpa isi `docs/context/reference/*` (enam dokumen acuan asli) — file-file ini read-only secara konvensi proyek.
- Menandai sebuah tiket `Done` jika salah satu poin Definition of Done belum terpenuhi.

## Setelah Tiket Selesai, AI Wajib Memperbarui

- [ ] `docs/tracking/CURRENT_TASK.md` dan `docs/tracking/DONE.md` (pindahkan tiket).
- [ ] `docs/tracking/CHANGELOG.md` (satu baris ringkas).
- [ ] File tiket itu sendiri di `tickets/` — checklist dicentang, status `Done`.
- [ ] `SESSION.md` sesi berjalan — Progress, Testing, File yang berubah, Next Session.
- [ ] `docs/decisions/DECISIONS.md` **hanya jika** ada keputusan teknis baru diambil selama implementasi (bukan untuk setiap tiket rutin).
- [ ] `docs/ai/AI_MEMORY.md` jika ada konteks kecil yang perlu diingat sesi berikutnya (bukan keputusan besar — itu ke `DECISIONS.md`).

## Jika AI Menemukan Inkonsistensi

Jangan diam-diam menyimpang dari dokumen acuan (Blueprint, FSD, TDD, Database Spec, UI/UX Spec, Implementation Guide) atau dari rules workspace. Catat sebagai entri baru di `docs/decisions/DECISIONS.md` § "Keputusan Baru Selama Implementasi" dengan format yang sudah ditentukan di file tersebut, sertakan alasan, baru lanjutkan implementasi sesuai keputusan yang dicatat.

## Konvensi Bahasa

- **Kode** (class, method, variable, commit message): selalu **Bahasa Inggris**.
- **Dokumentasi & komunikasi dengan developer**: **Bahasa Indonesia**, konsisten dengan seluruh dokumentasi proyek.

## Ticket Sizing — Batas yang Harus Ditegakkan AI

Saat AI diminta memecah EPIC/FEATURE menjadi tiket baru (lihat `docs/ai/AI_PROMPTS.md` § "Prompt Membuat Tiket Baru"):
- Satu TASK idealnya diselesaikan dalam **1 sesi kerja** (maksimal ± 1.5 hari solo developer paruh waktu).
- Jika sebuah TASK diperkirakan >2 hari, **pecah lebih lanjut** sebelum mulai dikerjakan — jangan mengerjakan tiket besar secara utuh.
- Setiap TASK harus memiliki tepat satu Coding Order yang jelas (bukan menggabungkan dua alur kerja tak berkaitan dalam satu tiket).
