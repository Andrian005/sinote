# AI_CONTEXT.md

> Konteks khusus untuk AI Assistant (Claude Code, GitHub Copilot, atau agent lain) yang bekerja di repository ini. File ini melengkapi (bukan menggantikan) `docs/context/PROJECT_CONTEXT.md`.

## Peran AI di Proyek Ini

AI Assistant berperan sebagai **pair programmer bagi solo developer** — bukan reviewer independen, bukan pengambil keputusan arsitektur baru. Peran utamanya:
1. Mengimplementasikan tiket yang sedang aktif sesuai spesifikasi yang **sudah ada** di 6 dokumen acuan.
2. Menegakkan Core Rules dan Development Rules secara konsisten (sesuatu yang mudah terlewat oleh manusia yang lelah/terburu-buru, tapi harus selalu ditegakkan AI).
3. Menjaga dokumentasi tracking (`CURRENT_TASK.md`, `DONE.md`, `CHANGELOG.md`) tetap sinkron dengan kode yang ditulis.

> **Aturan mengikat lengkap ada di `/AI_INSTRUCTIONS.md`** (root workspace) — file ini (`AI_CONTEXT.md`) memberi nuansa peran dan konteks, `AI_INSTRUCTIONS.md` adalah versi tegas yang menang jika ada perbedaan detail.

## Apa yang HARUS Dilakukan AI di Setiap Sesi

1. Baca `docs/context/PROJECT_CONTEXT.md`, `docs/rules/CORE_RULES.md`, `DEVELOPMENT_PLAYBOOK.md`, `docs/tracking/CURRENT_TASK.md`, `docs/decisions/DECISIONS.md` — **sebelum** menulis kode apa pun (lihat `AI_INSTRUCTIONS.md`).
2. Baca file tiket lengkap di `tickets/` yang sesuai dengan `CURRENT_TASK.md`.
3. Baca file rules spesifik yang relevan dengan pekerjaan saat ini (mis. `DATABASE_RULES.md` untuk migration, `UI_RULES.md` untuk Livewire).
4. Ikuti Coding Order (`CORE_RULES.md` § 2 / `DEVELOPMENT_PLAYBOOK.md` § 5) — logika sebelum UI, tanpa kecuali.

## Apa yang TIDAK BOLEH Dilakukan AI

Daftar lengkap ada di `AI_INSTRUCTIONS.md` § "AI Dilarang". Ringkasan tercepat:

- **Jangan** mengusulkan ulang keputusan yang sudah tercatat `Final` di `docs/decisions/DECISIONS.md`.
- **Jangan** membuat Repository class, Service class besar generik, atau Value Object untuk primitif sederhana.
- **Jangan** menulis Livewire/Blade sebelum Action terkait memiliki unit test yang hijau.
- **Jangan** melewati modul dalam Dependency Map (`docs/planning/ROADMAP.md`).
- **Jangan** membuat progress bar/metrik agregat menjadi input form manual.
- **Jangan** mengasumsikan aplikasi ini akan langsung multi-user.
- **Jangan** mengerjakan sesuatu di luar scope tiket aktif — catat sebagai tiket baru alih-alih dikerjakan langsung.

## Konvensi Bahasa

- **Kode** (nama class, method, variable, commit message): selalu **Bahasa Inggris**.
- **Dokumentasi** (file di `docs/`, komentar penjelasan panjang, tiket): **Bahasa Indonesia**, konsisten dengan seluruh 6 dokumen acuan proyek ini.

## Jika AI Menemukan Inkonsistensi

Jangan diam-diam menyimpang dari dokumen acuan. Catat sebagai entri baru di `docs/decisions/DECISIONS.md` (§ "Keputusan Baru Selama Implementasi") dengan format yang sudah ditentukan di file tersebut, sertakan alasan, baru lanjutkan implementasi sesuai keputusan yang dicatat.
