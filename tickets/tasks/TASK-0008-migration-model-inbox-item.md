# TASK-0008: Migration & Model InboxItem

- **ID:** TASK-0008
- **Judul:** Migration `inbox_items`, Enum InboxItemStatus, Model InboxItem
- **Deskripsi:** Membuat migration untuk tabel `inbox_items`, Enum untuk status lifecycle, dan Model `InboxItem` dengan relasi ke User. Tabel ini menampung capture spontan sebelum ditriase menjadi Task/Note/Project (FSD Modul 1).
- **Dependency:** TASK-0007 (EPIC-001 selesai penuh).
- **Priority:** Must Have — MVP 0 (v0.1).
- **Estimasi:** 0.5 hari.
- **Status:** `Done`

## Acceptance Criteria

- [x] Migration `inbox_items` dibuat dengan struktur sesuai Database Spec A.2:
  - `id` (ULID primary key)
  - `user_id` (ULID FK → users.id, restrict)
  - `content` (text, not null)
  - `status` (varchar(20), default `unprocessed`)
  - `converted_to_type` (varchar(30), nullable)
  - `converted_to_id` (ULID, nullable)
  - `processed_at` (timestamp, nullable)
  - `deleted_at` (timestamp, nullable — soft delete)
  - `created_at`, `updated_at` (timestamps)
- [x] Index composite `(user_id, status)` dibuat.
- [x] Check constraint `status IN ('unprocessed','processed','discarded')` diterapkan (PostgreSQL only — conditional).
- [x] Enum `InboxItemStatus` dibuat di `app/Domain/Inbox/Enums/InboxItemStatus.php` dengan backed case: `Unprocessed`, `Processed`, `Discarded`.
- [x] Model `InboxItem` dibuat di `app/Domain/Inbox/Models/InboxItem.php` dengan:
  - Trait `HasUlids`, `SoftDeletes`
  - Cast `status` ke `InboxItemStatus::class`
  - Cast `processed_at`, `deleted_at` ke `datetime`
  - `$fillable` eksplisit (tidak menggunakan guarded)
  - Relasi `belongsTo(User::class)`
  - Scope `unprocessed()`, `processed()`, `discarded()` untuk filter status
- [x] `php artisan migrate:fresh` berjalan tanpa error.
- [x] Skema tabel terverifikasi via `php artisan db:table inbox_items` atau tinker.

## Checklist Sebelum Mulai

- [x] Baca Database Spec A.2 lengkap.
- [x] Baca FSD Modul 1 (Inbox) untuk memahami business rules status lifecycle.

## Checklist Setelah Selesai

- [x] Migration order sesuai Database Spec Bagian G (inbox_items letakkan setelah `users`, sebelum `tags`).
- [x] Test unit untuk Model (relationship, scope) diverifikasi manual via tinker — berfungsi sempurna.
- [x] Status tiket diubah menjadi `Done`.
- [x] `DONE.md` dan `CHANGELOG.md` diperbarui.

## Catatan

Tidak seperti Tag yang polymorphic, InboxItem memiliki field informatif `converted_to_type`/`converted_to_id` yang **bukan** foreign key sungguhan (Database Spec Bagian E, poin 2) — ini sengaja, agar entitas hasil konversi dapat dihapus tanpa memutus integritas historis InboxItem.

Migration order yang tepat: setelah `users` (karena FK ke users), tapi sebelum atau sesudah `tags` (tidak ada dependency antar keduanya). Untuk konsistensi, letakkan setelah `users` dan sebelum `tags`.

