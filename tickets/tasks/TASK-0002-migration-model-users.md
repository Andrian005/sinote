# TASK-0002: Migration & Model `users`

- **ID:** TASK-0002
- **Judul:** Migration & Model `users`
- **Deskripsi:** Membuat migration tabel `users` sesuai Database Spec A.1 (ULID PK, email unique, tanpa soft delete pada tabel ini), Model `User` dengan relasi dasar yang akan dipakai seluruh entitas lain (`hasMany` ke setiap entitas utama, disiapkan meski relasinya belum ada modelnya).
- **Dependency:** TASK-0001.
- **Priority:** Must Have — blocking.
- **Estimasi:** 0.5 hari.
- **Status:** `Done`

## Acceptance Criteria

- [x] Migration `users`: `id` (ULID), `name`, `email` (unique), `email_verified_at` (nullable), `password`, `remember_token`, timestamps — **tanpa** `deleted_at` (sesuai Database Spec A.1: soft delete tidak diterapkan pada `users`).
- [x] Model `User` memakai trait `HasUlids` (atau setara) untuk ULID PK.
- [x] `$fillable` eksplisit didefinisikan (`name`, `email`, `password`).
- [x] Password otomatis di-hash lewat cast `hashed` (Laravel bawaan) — bukan mutator manual.
- [x] Factory `UserFactory` dasar dibuat.

## Checklist Sebelum Mulai

- [x] TASK-0001 selesai (environment aktif).

## Checklist Setelah Selesai

- [x] `php artisan migrate` berjalan tanpa error.
- [x] `User::factory()->create()` berhasil membuat baris via Tinker.
- [x] Commit: `core: add users migration and model`.
- [x] Pindahkan ke `DONE.md`, jadikan TASK-0003 sebagai `CURRENT_TASK.md` baru.
