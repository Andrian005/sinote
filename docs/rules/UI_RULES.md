# UI_RULES.md

*(Ringkasan operasional dari UI/UX Specification & Design System — untuk spesifikasi lengkap per halaman, baca dokumen asli di `docs/context/reference/05-uiux-design-system.md`)*

## Prinsip yang Mengikat Semua UI

**Clarity over density, progressive disclosure, calm technology, konsistensi lintas modul.** Ini bukan hanya slogan — setiap keputusan komponen di bawah ini adalah turunan langsung darinya.

## Aturan Komponen

1. **Satu komponen = satu implementasi dipakai ulang** (mis. `<x-task-row>` dipakai identik di Dashboard, Tasks, Project Detail). Jangan duplikasi styling row Task di berbagai tempat.
2. **Auto-save** untuk Note, Review reflection, dan Settings — **tidak** ada tombol "Simpan" manual di sana.
3. **Form submit eksplisit** untuk Task/Project/Goal — karena perubahannya lebih struktural, bukan tulisan bebas.
4. **Progress bar selalu read-only** — tidak pernah ada input manual untuk field ini di form manapun.
5. **Warna destructive (danger) dibuat pucat/muted**, bukan merah tegas — karena data reversibel via soft delete.
6. **Badge status selalu disertai teks label**, tidak hanya warna (aksesibilitas — pengguna buta warna).
7. **Dashboard tidak pernah menampilkan seluruh Task mentah** — selalu subset terkurasi (maks. 5–7 item per widget).
8. **Task tidak memiliki halaman navigasi primer sendiri** — selalu diakses lewat Dashboard atau Project (link "lihat semua").

## Design Tokens (Ringkas — Lihat Design System untuk Detail Penuh)

| Token | Nilai/Aturan |
|---|---|
| Radius default | `rounded-lg` (~8px); modal & Focus Mode: `rounded-xl`/`rounded-2xl` |
| Shadow/Elevation | 4 level: none / `shadow-sm` (Card) / `shadow-md` (dropdown) / `shadow-lg` (modal, toast) |
| Warna | Satu `primary` aksen tunggal; `neutral` basis slate/zinc; `success`/`warning`/`danger`/`info` muted |
| Font | Satu sans-serif (Inter/system font) |
| Dark mode | Wajib didukung sejak awal (Tailwind `dark:` variant), bukan ditunda |

## Empty/Loading/Error State — Wajib Ada di Setiap Halaman Baru

- **Empty state**: pesan positif dan spesifik konteks (bukan pesan generik "tidak ada data").
- **Loading state**: skeleton per widget/komponen (bukan spinner tunggal blocking seluruh halaman).
- **Error state**: kegagalan satu bagian tidak boleh menggagalkan bagian lain di halaman yang sama.

## Checklist Sebelum Livewire Component/Blade View Dianggap Selesai

- [ ] Empty/Loading/Error state ketiganya sudah diimplementasikan.
- [ ] Komponen memakai token warna/radius/shadow dari Design System, bukan nilai custom ad-hoc.
- [ ] Aksesibilitas dasar: ARIA label pada tombol icon-only, kontras teks memenuhi WCAG AA.
- [ ] Jika halaman ini butuh auto-save (Note/Review/Settings) — dipastikan sudah auto-save, bukan tombol simpan manual.
