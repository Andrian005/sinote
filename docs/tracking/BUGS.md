# BUGS.md

> Daftar bug yang ditemukan (termasuk dari dogfooding harian). Bug dengan tiket detail lengkap ada di `tickets/bugs/`. File ini adalah indeks cepat status seluruh bug.

## Format Entri

```
| ID | Judul | Modul Terdampak | Severity | Status |
```

## Bug Aktif

| ID | Judul | Modul Terdampak | Severity | Status |
|---|---|---|---|---|
| *(belum ada bug tercatat)* | | | | |

## Severity Levels

- **Critical** — data hilang/rusak, atau blocking seluruh alur inti (Capture→Organize→Prioritize→Execute).
- **High** — satu modul tidak berfungsi, ada workaround manual.
- **Medium** — perilaku tidak sesuai spesifikasi FSD tapi tidak menghalangi pemakaian.
- **Low** — kosmetik/UI minor.

## Aturan

- Bug **Critical** dan **High** wajib diperbaiki sebelum melanjutkan ke tiket/modul berikutnya (lihat `DEVELOPMENT_RULES.md`).
- Bug **Medium**/**Low** dapat ditunda dan dicatat sebagai technical debt di `docs/decisions/DECISIONS.md` jika sengaja ditunda.
