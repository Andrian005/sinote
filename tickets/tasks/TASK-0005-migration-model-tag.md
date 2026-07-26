# TASK-0005: Migration `tags` + `taggables` & Model Tag

- **ID:** TASK-0005
- **Judul:** Migration `tags` + `taggables` & Model `Tag`
- **Deskripsi:** Membuat migration dan Model `Tag` beserta relasi polymorphic `morphToMany`, mengikuti Database Spec A.9. Ini adalah langkah fondasi EPIC-001 — seluruh tiket EPIC-001 berikutnya bergantung pada tabel dan Model yang dibuat di sini. Mencakup Migration → Enum (tidak ada) → Model + Relationship sesuai Coding Order.
- **Dependency:** TASK-0004 (EPIC-000 selesai penuh).
- **Priority:** Must Have — blocking TASK-0006 dan seluruh modul yang menggunakan Tag.
- **Estimasi:** 0.5–1 hari.
- **Status:** `Done`
- **EPIC Induk:** EPIC-001 — Tagging & Context

## Acceptance Criteria

- [x] Migration `create_tags_table` membuat tabel `tags` dengan kolom: `id` (ULID, PK), `user_id` (ULID, FK → `users.id` cascade on delete), `name` (varchar 50), `created_at`, `updated_at`.
- [x] Unique constraint `(user_id, name)` terpasang di tabel `tags`.
- [x] Migration `create_taggables_table` membuat tabel pivot `taggables` dengan kolom: `tag_id` (FK → `tags.id` cascade on delete), `taggable_id` (ULID), `taggable_type` (varchar 50).
- [x] Unique constraint `(tag_id, taggable_id, taggable_type)` terpasang di tabel `taggables`.
- [x] Index `(taggable_type, taggable_id)` dan `(tag_id)` terpasang di tabel `taggables`.
- [x] Model `Tag` berada di `app/Domain/Shared/Models/Tag.php` dengan: `$fillable = ['user_id', 'name']`, cast ULID untuk `id`, relasi `belongsTo User`, relasi `morphedByMany` untuk tiap entitas taggable (Task, Project, Note, Habit — stub untuk modul yang belum ada tidak perlu, cukup pola relasi yang benar).
- [x] `php artisan migrate:fresh` berjalan tanpa error.
- [x] Kolom `name` disimpan dalam bentuk lowercase (normalisasi dilakukan di level Model/Action — verifikasi pola ini terdokumentasi di kode sebagai komentar).

## Checklist Sebelum Mulai

- [x] TASK-0004 berstatus `Done`.
- [x] Database Spec A.9 sudah dibaca (lokasi: `docs/context/reference/04-database-business-rules.md`).
- [x] Migration Order Spec bagian G sudah diverifikasi (`tags` di posisi 2, setelah `users`).

## Checklist Setelah Selesai

- [x] `php artisan migrate:fresh` berjalan bersih.
- [x] Tinker: `App\Domain\Shared\Models\Tag::class` bisa di-instantiate tanpa error.
- [x] Commit: `tagging: add tags and taggables migrations and Tag model`.
- [x] Pindahkan ke `DONE.md`.
- [x] Jadikan TASK-0006 sebagai `CURRENT_TASK.md` baru.

## Catatan Teknis

- Tabel `taggables` tidak memiliki primary key tersendiri dan tidak menggunakan soft delete (pivot murni — Database Spec A.9).
- `user_id` on `tags`: cascade on delete (bukan restrict) karena tag adalah milik langsung user, tanpa entitas dependen yang bermakna berdiri sendiri (berbeda dengan Task yang di-set null saat Project dihapus).
- Nama Model: `Tag` (bukan `Context` atau `Label`) — konsisten dengan Database Spec dan FSD Modul 4.
- Namespace: `App\Domain\Shared\Models\Tag` — mengikuti struktur domain yang sudah ditetapkan di TASK-0001.
