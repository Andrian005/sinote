# GIT_RULES.md

*(Ringkasan operasional dari TDD bagian 35–37 dan Implementation Guide bagian 8)*

## Branch Strategy

```
main            → selalu deployable, tidak pernah rusak
feature/*       → satu branch per fitur/tiket kecil (mis. feature/tasks-completion)
fix/*           → perbaikan bug spesifik (mis. fix/streak-reset-bug)
```

Tidak ada `develop`/`release` branch terpisah — tidak ada tim lain yang perlu dikoordinasikan lewat percabangan kompleks.

## Commit Message

Format: `{modul}: {deskripsi singkat, present tense}`

Contoh baik:
```
tasks: add streak recalculation on habit check-in
inbox: fix validation for empty capture text
projects: implement automatic progress calculation
```

Satu commit = satu perubahan logis. Hindari commit besar "banyak perubahan sekaligus".

## Alur Kerja Branch per Tiket

1. Branch baru dari `main` terbaru: `git checkout -b feature/{nama-tiket}`.
2. Commit granular selama implementasi (ikuti Coding Order).
3. Jalankan Laravel Pint + test suite lokal sebelum push.
4. Push, biarkan CI berjalan (lint + test + `composer audit`).
5. Jika CI hijau → merge ke `main`.
6. Hapus branch fitur setelah merge (menjaga daftar branch tetap bersih).

## CI Wajib Lolos Sebelum Merge

- [ ] Laravel Pint (format check).
- [ ] Test suite Pest.
- [ ] `composer audit` (vulnerability dependency).

## Larangan

- Jangan commit langsung ke `main` tanpa melalui branch fitur (kecuali perbaikan dokumentasi murni yang tidak menyentuh kode).
- Jangan force-push ke `main`.
- Jangan biarkan branch fitur hidup lebih dari ~1 sprint (branch berumur pendek, sesuai TDD bagian 36).
