# Development Workflow & AI Workflow

> **Catatan:** `DEVELOPMENT_PLAYBOOK.md` (root workspace) adalah dokumen utama yang merangkai isi file ini secara lebih ringkas bersama Definition of Ready/Done, Quality Gate, dan Review checklist. Aturan AI yang mengikat ada di `AI_INSTRUCTIONS.md`. File ini tetap valid sebagai penjelasan naratif langkah-demi-langkah — jika ada perbedaan detail dengan Playbook, Playbook yang menang.

## Development Workflow (Harian)

Ikuti urutan ini setiap sesi kerja, tanpa melompati langkah:

0. **Membuka Sesi** — salin template dari `SESSION.md`, isi Tanggal/Target/Ticket sebelum mulai.
1. **Memilih Tiket** — buka `docs/tracking/CURRENT_TASK.md`. Jika kosong, ambil tiket teratas dari `docs/tracking/NEXT_TASK.md`, atau dari `tickets/` sesuai urutan Sprint di `docs/planning/ROADMAP.md`.
2. **Membaca Konteks** — buka file tiket lengkap di folder `tickets/` yang sesuai (EPIC/FEATURE/TASK/BUG/CHORE/REFACTOR), baca Dependency dan Acceptance Criteria-nya. Jika tiket menyentuh area yang punya ADR terkait, baca ADR tersebut di `docs/decisions/adr/`.
3. **Implementasi** — mengikuti Coding Order dari Implementation Guide bagian 5 (Migration → Enum → Model → Factory → Policy → Form Request → Action → Event/Listener → Notification/Job → Livewire/UI → Feature Test → Seeder). Logika dulu, UI belakangan.
4. **Testing** — jalankan unit test untuk Action yang ditulis; jalankan feature test untuk alur end-to-end tiket ini. Tidak lanjut ke langkah berikutnya jika test gagal.
5. **Review** — jalankan checklist di `docs/rules/DEVELOPMENT_RULES.md` § Code Review Checklist terhadap kode sendiri (solo developer berperan sebagai reviewer).
6. **Documentation** — perbarui file dokumentasi yang relevan (lihat AI Workflow di bawah — aturan yang sama berlaku untuk manusia).
7. **Commit** — commit granular mengikuti `docs/rules/GIT_RULES.md`, pesan format `{modul}: {deskripsi singkat}`.
8. **Update `docs/tracking/DONE.md`** — pindahkan tiket yang selesai beserta tanggal penyelesaian.
9. **Menentukan `docs/tracking/NEXT_TASK.md`** — ambil tiket berikutnya sesuai Dependency Map (Implementation Guide bagian 6), perbarui `CURRENT_TASK.md` untuk sesi berikutnya.
10. **Menutup Sesi** — lengkapi `SESSION.md` (Progress, Kendala, Solusi, Keputusan, File yang berubah, Testing, Catatan, Next Session).

Jika di tengah langkah manapun ditemukan hambatan yang tidak bisa diselesaikan hari itu, catat di `docs/tracking/BLOCKERS.md` sebelum mengakhiri sesi — jangan biarkan blocker hanya "diingat di kepala".

---

## AI Workflow

Setiap kali AI Assistant (Claude Code, Copilot, atau agent lain) memulai sesi kerja pada proyek ini, AI **wajib** membaca urutan berikut sebelum menulis satu baris kode:

1. `docs/context/PROJECT_CONTEXT.md` — memahami apa proyek ini dan batasannya.
2. `docs/rules/CORE_RULES.md` — memahami aturan yang tidak boleh dilanggar.
3. `DEVELOPMENT_PLAYBOOK.md` — memahami filosofi dan seluruh alur kerja proyek.
4. `docs/tracking/CURRENT_TASK.md` — memahami tiket apa yang sedang dikerjakan saat ini.
5. `docs/decisions/DECISIONS.md` — memahami keputusan apa saja yang sudah final dan tidak boleh diusulkan ulang.

Aturan lengkap dan mengikat untuk AI ada di `AI_INSTRUCTIONS.md` — bagian di bawah ini adalah ringkasannya.

**Setelah membaca keempatnya**, AI boleh membaca file tiket detail di `tickets/`, dan file rules spesifik yang relevan dengan modul yang sedang dikerjakan (mis. `DATABASE_RULES.md` saat menulis migration, `UI_RULES.md` saat menulis Livewire component).

### Aturan Tambahan untuk AI

- **AI tidak boleh mengusulkan ulang** keputusan arsitektur yang sudah tercatat final di `docs/decisions/DECISIONS.md` atau ADR terkait, kecuali diminta eksplisit oleh developer untuk meninjau ulang.
- **AI tidak boleh melompati urutan Coding Order** (lihat Development Workflow langkah 3) demi "kecepatan" — logika bisnis harus diuji sebelum UI ditulis.
- **Setelah menyelesaikan satu tiket**, AI wajib memperbarui:
  - `docs/tracking/CURRENT_TASK.md` dan `docs/tracking/DONE.md` (pindahkan tiket).
  - `docs/tracking/CHANGELOG.md` (satu baris ringkas perubahan).
  - File tiket itu sendiri di `tickets/` — checklist dicentang, status diubah menjadi `Done`.
  - `docs/decisions/DECISIONS.md` **hanya jika** ada keputusan teknis baru yang diambil selama implementasi (bukan untuk setiap tiket).
- **AI tidak boleh menulis dokumentasi baru yang bertentangan** dengan 6 dokumen acuan (Blueprint, FSD, TDD, Database Spec, UI/UX Spec, Implementation Guide) — jika AI menemukan inkonsistensi, itu dicatat sebagai entri baru di `docs/decisions/DECISIONS.md` dengan alasan, bukan diam-diam diubah.
- **AI selalu menyimpan konteks penting** yang muncul selama sesi (keputusan kecil, alasan suatu pendekatan dipilih di tengah implementasi) ke `docs/ai/AI_MEMORY.md`, agar sesi berikutnya (baik AI yang sama maupun berbeda) tidak kehilangan konteks tersebut.
