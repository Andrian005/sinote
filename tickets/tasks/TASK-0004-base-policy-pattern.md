# TASK-0004: Base Policy Pattern & Contoh Penerapan

- **ID:** TASK-0004
- **Judul:** Base Policy Pattern & Contoh Penerapan
- **Deskripsi:** Menetapkan pola Policy standar (aturan dasar: `view/update/delete → $entity->user_id === $user->id`) yang akan dipakai ulang identik di seluruh Policy entitas berikutnya (TDD bagian 20). Menutup EPIC-000.
- **Dependency:** TASK-0003.
- **Priority:** Must Have — blocking seluruh modul fitur berikutnya.
- **Estimasi:** 1–1.5 hari.
- **Status:** `Done`

## Acceptance Criteria

- [x] Dokumentasi pola Policy standar ditulis sebagai referensi cepat (boleh berupa contoh kode pendek di `docs/rules/LARAVEL_RULES.md` — sudah ada, verifikasi masih akurat).
- [x] Satu Policy contoh dibuat dan diuji terhadap Model dummy/`User` itu sendiri untuk memverifikasi pola `$user->id === $entity->user_id` bekerja sebagaimana mestinya sebelum dipakai ulang di Policy sungguhan (TaskPolicy, ProjectPolicy, dst. akan dibuat masing-masing saat modulnya dimulai).
- [x] Middleware `auth` diverifikasi aktif di seluruh route group utama.
- [x] Struktur folder `app/Domain/*` (dibuat di TASK-0001) diverifikasi konsisten dengan namespace yang akan dipakai Policy (`App\Policies\*`).

## Checklist Sebelum Mulai

- [x] TASK-0003 selesai.

## Checklist Setelah Selesai

- [x] Pola Policy terverifikasi bekerja lewat test sederhana.
- [x] Commit: `core: establish base policy pattern`.
- [x] Pindahkan ke `DONE.md`.
- [x] **EPIC-000 (Core Infrastructure) selesai penuh** — perbarui status EPIC-000 menjadi `Done`.
- [x] Jadikan FEAT-0001 (transisi ke EPIC-001) sebagai `CURRENT_TASK.md` baru.
