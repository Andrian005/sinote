# AI_PROMPTS.md

> Kumpulan prompt siap pakai untuk memulai sesi kerja dengan AI Assistant (Claude Code, Copilot Chat, dsb.) di repository ini. Salin-tempel sesuai kebutuhan sesi.

## Prompt Awal Sesi (Wajib Dipakai Setiap Sesi Baru)

```
Baca berurutan sebelum melakukan apa pun:
1. docs/context/PROJECT_CONTEXT.md
2. docs/rules/CORE_RULES.md
3. DEVELOPMENT_PLAYBOOK.md
4. docs/tracking/CURRENT_TASK.md
5. docs/decisions/DECISIONS.md

Aturan mengikat lengkap ada di AI_INSTRUCTIONS.md — patuhi selama sesi ini.

Setelah membaca kelimanya, salin template dari SESSION.md dan isi Tanggal/Target/Ticket
untuk sesi ini, lalu konfirmasi ke saya: tiket apa yang sedang aktif, dan langkah pertama
apa yang akan kamu lakukan sesuai Coding Order (DEVELOPMENT_PLAYBOOK.md § 5).
Jangan menulis kode apa pun sebelum konfirmasi ini.
```

## Prompt Mengerjakan Tiket

```
Kerjakan tiket {ID_TIKET} (file lengkap di tickets/{jenis}/{ID_TIKET}-*.md).
Ikuti Coding Order di docs/rules/CORE_RULES.md (Migration → Enum → Model → Factory →
Policy → Form Request → Action+unit test → Event/Listener → Notification/Job →
Livewire/UI → Feature Test → Seeder).
Sebelum menulis Livewire/UI, tunjukkan dulu unit test Action yang sudah hijau.
```

## Prompt Setelah Tiket Selesai

```
Tiket {ID_TIKET} sudah selesai. Lakukan:
1. Perbarui checklist Acceptance Criteria di file tiket menjadi tercentang, ubah status
   menjadi Done.
2. Pindahkan entri dari docs/tracking/CURRENT_TASK.md ke docs/tracking/DONE.md.
3. Tambahkan satu baris ke docs/tracking/CHANGELOG.md.
4. Jika ada keputusan teknis baru yang diambil selama implementasi, catat di
   docs/decisions/DECISIONS.md.
5. Tentukan tiket berikutnya dari docs/tracking/NEXT_TASK.md dan jadikan itu
   CURRENT_TASK.md yang baru.
6. Jika ada konteks kecil yang perlu diingat sesi berikutnya, catat di docs/ai/AI_MEMORY.md.
7. Lengkapi SESSION.md sesi ini: Progress, Kendala, Solusi, Keputusan, File yang berubah,
   Testing, Catatan, Next Session.
```

## Prompt Review Kode Sendiri

```
Tinjau kode yang baru ditulis untuk tiket {ID_TIKET} terhadap Code Review Checklist di
docs/rules/DEVELOPMENT_RULES.md. Laporkan poin mana yang belum terpenuhi, jangan
memperbaiki otomatis tanpa konfirmasi jika perbaikannya mengubah pendekatan yang sudah ada.
```

## Prompt Membuat Tiket Baru (Memecah EPIC)

```
Pecah EPIC-{nomor} (file di tickets/epics/) menjadi FEATURE dan TASK mengikuti pola di
tickets/tasks/TASK-0001 sampai TASK-0004 sebagai template. Setiap tiket harus punya ID,
Judul, Deskripsi, Acceptance Criteria, Dependency, Priority, Estimasi, Status, Checklist.
Rujuk FSD dan TDD untuk detail modul terkait sebelum menulis Acceptance Criteria.
```

## Prompt Investigasi Bug

```
Bug {ID_BUG} (file di tickets/bugs/) perlu diinvestigasi. Baca dulu FSD bagian modul
terkait untuk memahami perilaku yang seharusnya terjadi (Business Rules, Edge Cases,
State Machine), baru bandingkan dengan kode saat ini untuk menemukan penyebabnya.
Jangan langsung menambal gejala tanpa memahami akar masalah terhadap spesifikasi.
```
