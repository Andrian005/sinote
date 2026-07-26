# AI_MEMORY.md

> Memori kerja lintas-sesi untuk AI Assistant — tempat mencatat konteks kecil yang muncul **selama implementasi** dan tidak cukup signifikan untuk masuk `docs/decisions/DECISIONS.md`, tapi penting agar sesi berikutnya (AI yang sama atau berbeda) tidak kehilangan konteks. Diperbarui oleh AI sendiri di akhir sesi (lihat `WORKFLOW.md` § AI Workflow).

## Format Entri

```
### {tanggal} — {ringkasan singkat}
- Konteks tiket: {ID tiket saat itu}
- Catatan: {apa yang perlu diingat sesi berikutnya}
```

## Aturan Pengisian

- Catat **hal-hal kecil yang mudah terlupakan**: alasan suatu pendekatan dipilih di tengah implementasi (bukan keputusan besar — itu ke `DECISIONS.md`), workaround sementara yang perlu ditinjau ulang, pola/perilaku package pihak ketiga yang perlu diingat.
- **Jangan** duplikasi isi `DECISIONS.md`, `BLOCKERS.md`, atau `BUGS.md` di sini — file ini murni untuk konteks kerja yang tidak masuk kategori manapun.
- Bersihkan entri yang sudah tidak relevan (mis. terkait tiket yang sudah lama selesai dan konteksnya sudah tidak dibutuhkan) secara berkala saat sesi maintenance.

---

### 2026-07-25 — File `public/hot` menyebabkan CSS hilang
- Konteks tiket: TASK-0003
- Catatan: File `public/hot` (sisa Vite dev server) membuat Laravel mencoba load asset dari Vite dev server yang tidak berjalan, akibatnya CSS Tailwind tidak termuat. **Jika styling hilang di environment lokal, selalu cek dan hapus `public/hot` dulu** atau jalankan `php artisan optimize:clear`.

### 2026-07-25 — Tailwind CSS v4 incompatible dengan v3
- Konteks tiket: TASK-0003
- Catatan: `@tailwindcss/vite` versi 4.x membutuhkan `tailwindcss` versi 4.x juga. Package.json semula punya `"tailwindcss": "^3.1.0"` + `"@tailwindcss/vite": "^4.0.0"` — ini menyebabkan error `Can't resolve 'tailwindcss'`. Solusi: upgrade `tailwindcss` ke `^4.0.0`.

### 2026-07-25 — `App\Models\User` vs `App\Domain\Shared\Models\User`
- Konteks tiket: TASK-0003
- Catatan: Scaffolding Breeze menggunakan `use App\Models\User` yang tidak valid karena proyek memindahkan User ke `App\Domain\Shared\Models\User`. Setiap kali Breeze/Fortify membuat file baru (atau ada stubs), import User harus diverifikasi dan diperbaiki ke `App\Domain\Shared\Models\User`.
