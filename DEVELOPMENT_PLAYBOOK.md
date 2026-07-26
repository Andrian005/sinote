# DEVELOPMENT_PLAYBOOK.md

> **Dokumen utama proses development.** Ini adalah satu-satunya tempat yang menyatukan filosofi, prinsip, dan seluruh workflow proyek dalam satu alur baca. File `docs/rules/*.md` tetap menjadi rujukan detail per topik (arsitektur, database, UI, dst.) — playbook ini merangkai potongan-potongan itu menjadi satu cerita utuh tentang "bagaimana proyek ini dikerjakan setiap hari".

---

## 1. Development Philosophy

Personal OS dibangun dengan filosofi **Foundation-First Vertical Slicing**: fondasi (Auth, `user_id` scoping, Tagging) harus 100% berdiri sebelum thin slice fitur pertama dimulai, setelah itu setiap slice dibangun secara vertikal — menghasilkan sesuatu yang bisa langsung didogfooding, bukan modul demi modul yang baru terasa berguna di akhir.

Tiga nilai yang tidak bisa dikompromikan:
- **Boring technology first** — pilih yang matang & terdokumentasi baik, bukan yang trendi.
- **Logika sebelum tampilan** — Action harus benar dan teruji sebelum disambungkan ke Livewire.
- **Bukti pemakaian nyata di atas asumsi** — jangan optimasi atau bangun fitur untuk skala yang belum tentu terjadi.

## 2. Engineering Principles

1. Satu Action = satu tanggung jawab. Tanpa Repository Pattern. Tanpa Service class generik besar (`docs/rules/ARCHITECTURE_RULES.md`).
2. Isolasi data per user wajib di setiap query, sejak hari pertama meski masih single-user.
3. Progres/metrik agregat selalu dihitung otomatis — tidak pernah jadi input manual.
4. Setiap status memakai backed Enum, setiap efek lintas modul memakai Event/Listener.
5. Konsistensi penamaan dan struktur adalah bentuk dokumentasi paling murah untuk solo developer (`docs/coding/NAMING_CONVENTION.md`).
6. Keputusan final tidak diusulkan ulang tanpa alasan baru yang eksplisit (`docs/decisions/DECISIONS.md`).

## 3. Daily Workflow

```
1. Buka SESSION.md → salin template, isi Tanggal & Target sesi ini
2. Baca CURRENT_TASK.md → pastikan tiket aktif jelas
3. Kerjakan tiket mengikuti Coding Order (§ Developer Workflow di bawah)
4. Testing sesuai TESTING_RULES.md
5. Review kode sendiri dengan Code Review Checklist (§ 13 di bawah)
6. Perbarui dokumentasi yang relevan
7. Commit sesuai GIT_RULES.md
8. Isi sisa SESSION.md (Progress, Kendala, Solusi, Next Session)
9. Update DONE.md, CHANGELOG.md, NEXT_TASK.md → CURRENT_TASK.md baru
```

## 4. AI Workflow

Lihat `AI_INSTRUCTIONS.md` untuk aturan lengkap dan mengikat. Ringkasan: AI membaca `PROJECT_CONTEXT.md → CORE_RULES.md → DEVELOPMENT_PLAYBOOK.md (dokumen ini) → CURRENT_TASK.md → DECISIONS.md` sebelum menulis kode, mengerjakan **hanya** scope tiket aktif, dan memperbarui dokumentasi tracking di sesi yang sama dengan penyelesaian tiket.

## 5. Developer Workflow (Coding Order per Tiket)

Mengikuti Implementation Guide bagian 5, berlaku di dalam **setiap** tiket TASK/FEATURE:

```
Migration → Enum → Model+Relationship → Factory → Policy → Form Request
→ Action (+unit test) → Event & Listener → Notification/Job
→ Livewire Component + Blade View → Feature Test → Seeder (jika relevan)
```

**Alasan urutan ini tidak berubah:** logika bisnis harus benar dan teruji sebelum UI menyembunyikan bug di baliknya (`CORE_RULES.md` § 2).

## 6. Ticket Workflow

1. Ambil tiket dari `CURRENT_TASK.md` (atau `NEXT_TASK.md`/`BACKLOG.md` jika kosong).
2. Baca file tiket lengkap di `tickets/{jenis}/` — Dependency, Acceptance Criteria, Checklist.
3. Jika tiket menyentuh keputusan arsitektur, baca ADR terkait di `docs/decisions/adr/`.
4. Kerjakan sesuai Developer Workflow § 5.
5. Centang Acceptance Criteria dan Checklist di file tiket, ubah `Status` menjadi `Done`.
6. Pindahkan referensi ke `DONE.md`.

**Saat sebuah EPIC akan dimulai** (belum dipecah menjadi TASK), gunakan pola FEAT-0001 (`tickets/features/`) sebagai contoh tiket kickoff/pemecahan — jangan memecah seluruh backlog di muka (lihat `BACKLOG.md`).

## 7. Documentation Workflow

- Dokumentasi tracking (`CURRENT_TASK.md`, `NEXT_TASK.md`, `DONE.md`, `CHANGELOG.md`, `SESSION.md`) diperbarui **di sesi yang sama** dengan penyelesaian kerja — bukan "nanti".
- Dokumentasi keputusan (`DECISIONS.md`, ADR baru) ditambahkan **hanya** saat ada keputusan teknis baru — jangan menulis ADR untuk setiap tiket kecil.
- Dokumentasi acuan (`docs/context/reference/*`, enam dokumen asli) **tidak pernah diedit** — jika ditemukan tidak akurat lagi, itu dicatat sebagai penyimpangan di `DECISIONS.md`, bukan mengubah dokumen sumbernya.

## 8. Refactoring Rules

- Refactoring dilakukan **setelah** satu unit kerja selesai (tiket tercentang penuh), bukan di tengah penulisan satu Action — mencegah kehilangan momentum.
- Utang teknis diperbolehkan sadar hanya di luar fondasi (styling, fitur pencarian sederhana) — **tidak pernah** pada `user_id`, Policy, atau desain relasi.
- Setiap utang yang diambil dicatat eksplisit (`// TODO(debt): alasan`) dan ditinjau saat Monthly Review (§ 18).
- Refactoring murni **tidak boleh** mengubah perilaku eksternal — test yang ada sebelum refactoring harus tetap hijau tanpa perubahan assertion setelahnya (lihat `tickets/refactors/REFACTOR-0001` sebagai contoh).

## 9. Testing Rules

Ringkasan dari `docs/rules/TESTING_RULES.md` — prioritas pada risiko tertinggi, bukan cakupan 100%:
- **Wajib unit test:** transisi State Machine, kalkulasi agregat (progres Project, streak Habit), snapshot freeze (Review), kondisi kirim/skip reminder.
- **Feature test:** alur end-to-end per Use Case FSD.
- **Manual/dogfooding cukup:** modul UI-murni tanpa logika bisnis kompleks (Focus Mode, styling Dashboard).

## 10. Git Workflow

Ringkasan dari `docs/rules/GIT_RULES.md`: `main` selalu deployable, `feature/*` dan `fix/*` berumur pendek, commit granular `{modul}: {deskripsi singkat}`, CI (Pint + Pest + `composer audit`) wajib lolos sebelum merge.

## 11. Definition of Ready

Sebuah tiket **siap dikerjakan** jika:
- [ ] ID, Judul, Deskripsi jelas dan tidak ambigu.
- [ ] Dependency tercantum dan **sudah** berstatus Done (bukan sedang berjalan).
- [ ] Acceptance Criteria dapat diverifikasi objektif (bukan "harus bagus", tapi "X terjadi ketika Y").
- [ ] Estimasi masuk akal untuk satu-dua sesi kerja solo developer (lihat § Ticket Sizing di `AI_INSTRUCTIONS.md`/hasil audit).
- [ ] Modul/entitas terkait sudah punya bagian relevan di FSD/Database Spec yang bisa dirujuk.

## 12. Definition of Done

Sebuah tiket **selesai** jika (identik dengan `docs/rules/DEVELOPMENT_RULES.md` § Definition of Done):
1. Seluruh Acceptance Criteria tercentang.
2. Unit test (jika berlaku) hijau.
3. Feature test (jika berlaku) hijau.
4. Code Review Checklist (§ 13) sudah dijalankan.
5. `DONE.md` dan `CHANGELOG.md` sudah diperbarui.
6. Status tiket di `tickets/` diubah menjadi `Done`.
7. `SESSION.md` sesi ini sudah diisi lengkap.

## 13. Quality Gate & Code Review Checklist

Tidak ada tiket yang dianggap selesai jika salah satu gate ini gagal:

- [ ] **Naming Convention** sesuai `docs/coding/NAMING_CONVENTION.md`.
- [ ] **Clean Code** — satu Action satu tanggung jawab, tanpa method >30–40 baris tanpa alasan.
- [ ] **SOLID** — khususnya Single Responsibility & Dependency Inversion.
- [ ] **Performance** — eager loading diterapkan, tidak ada query dalam loop.
- [ ] **Security** — `$fillable` eksplisit, Policy dipanggil sebelum Action, input tervalidasi.
- [ ] **Validation** — seluruh input lewat Form Request sesuai FSD.
- [ ] **Error Handling** — Exception domain spesifik untuk kondisi bisnis yang teridentifikasi.
- [ ] **Logging** — Event penting lintas modul tercatat.
- [ ] **Documentation** — nama self-documenting, dokumentasi tracking diperbarui.
- [ ] **Tidak melanggar Core Rules** (`docs/rules/CORE_RULES.md`) — cek ulang delapan aturan intinya.

## 14. Release Checklist

Sebuah versi **tidak** dianggap layak dirilis (lihat `docs/planning/RELEASE_PLAN.md`) tanpa:
- [ ] Seluruh EPIC dalam milestone terkait berstatus Done.
- [ ] CI hijau penuh (Pint, Pest, `composer audit`).
- [ ] `.env` production terverifikasi lengkap.
- [ ] Backup otomatis aktif **dan** sudah diuji restore minimal satu kali.
- [ ] Monitoring (Sentry/setara) terpasang.
- [ ] Tidak ada bug **Critical**/**High** terbuka di `BUGS.md`.
- [ ] `RELEASE_PLAN.md` § Riwayat Rilis diperbarui.

## 15. Daily Routine

- Mulai sesi: isi `SESSION.md` bagian Tanggal/Target/Ticket.
- Selama sesi: ikuti Coding Order, jangan lompat langkah.
- Akhiri sesi: `SESSION.md` terisi penuh, `DONE.md`/`CHANGELOG.md`/`NEXT_TASK.md` sinkron dengan kondisi nyata, **meski tiket belum selesai** (catat progres apa adanya).

## 16. Weekly Review

Dilakukan di akhir setiap sprint mingguan (`docs/planning/ROADMAP.md`):
- [ ] Tinjau seluruh `SESSION.md` minggu ini — pola kendala berulang?
- [ ] Tinjau `BUGS.md` dan `BLOCKERS.md` — ada yang menumpuk tanpa resolusi?
- [ ] Tinjau progres terhadap Milestone aktif (`docs/planning/MILESTONES.md`) — realistis mengejar target sprint berikutnya?
- [ ] Perbarui `ROADMAP.md` jika urutan perlu disesuaikan (dengan alasan tercatat di `DECISIONS.md` jika menyimpang dari Implementation Guide).

## 17. Monthly Review

Mengikuti Implementation Guide bagian 14 (Maintenance Guide):
- [ ] `composer outdated`/`npm outdated` ditinjau (lihat contoh `tickets/chores/CHORE-0001`).
- [ ] Uji restore backup.
- [ ] Tinjau log channel `security`.
- [ ] Tinjau utang teknis yang dicatat (`// TODO(debt)`) — mana yang layak dilunasi bulan ini.
- [ ] Bersihkan entri usang di `docs/ai/AI_MEMORY.md`.
- [ ] Tinjau index database yang jarang terpakai (jika sudah ada data produksi nyata).

---

*Dokumen ini merangkai `docs/rules/*.md`, `docs/coding/*.md`, dan `docs/tracking/*.md` menjadi satu alur — jika ada pertentangan antara ringkasan di sini dan file rules aslinya, file rules asli yang menang; perbarui playbook ini agar selaras kembali.*
