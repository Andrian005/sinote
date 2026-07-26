# TASK-0006: Factory + Policy + Action Tag + Unit Test

- **ID:** TASK-0006
- **Judul:** Factory + Policy + Action `CreateTag` / `AttachTag` / `DetachTag` + Unit Test
- **Deskripsi:** Membangun seluruh logika bisnis Tag: `TagFactory` untuk keperluan test, `TagPolicy` untuk isolasi data per user, dan tiga Action (`CreateTag`, `AttachTag`, `DetachTag`) beserta unit test-nya. Mengikuti Coding Order: Factory → Policy → Form Request → Action + unit test. Tidak ada UI di tiket ini.
- **Dependency:** TASK-0005 (tabel `tags` + `taggables` dan Model `Tag` tersedia).
- **Priority:** Must Have — blocking TASK-0007 (UI) dan seluruh modul yang butuh menempelkan Tag ke entitas.
- **Estimasi:** 1–1.5 hari.
- **Status:** `Done`
- **EPIC Induk:** EPIC-001 — Tagging & Context

## Acceptance Criteria

- [x] `TagFactory` dibuat di `database/factories/Domain/Shared/TagFactory.php`, menghasilkan `Tag` valid dengan `name` lowercase unik per user.
- [x] `TagPolicy` dibuat di `app/Policies/TagPolicy.php` dengan method `view`, `update`, `delete` — semua mengikuti pola: `$tag->user_id === $user->id`.
- [x] `TagPolicy` terdaftar di `AuthServiceProvider` (atau auto-discovery Laravel).
- [x] `StoreTagRequest` (Form Request) memvalidasi: `name` wajib, 1–50 karakter, tanpa koma (sesuai FSD 4.1 Validation Rules).
- [x] `CreateTag` Action (`app/Domain/Shared/Actions/CreateTag.php`): menerima `user_id` + `name`, menormalisasi ke lowercase, mengembalikan Tag existing jika nama sudah ada (case-insensitive), membuat baru jika belum ada — **tidak pernah** membuat duplikat.
- [x] `AttachTag` Action (`app/Domain/Shared/Actions/AttachTag.php`): menerima `Tag` + model taggable (polymorphic), melekatkan relasi — idempoten (memanggil dua kali tidak menghasilkan duplikat di `taggables`).
- [x] `DetachTag` Action (`app/Domain/Shared/Actions/DetachTag.php`): menerima `Tag` + model taggable, melepas relasi.
- [x] Unit test untuk `CreateTag`: (a) membuat tag baru, (b) mengembalikan tag existing jika nama sama (case-insensitive), (c) menyimpan nama dalam bentuk lowercase.
- [x] Unit test untuk `AttachTag`: (a) relasi tersimpan di `taggables`, (b) idempoten — dua kali attach tidak dobel.
- [x] Unit test untuk `DetachTag`: (a) relasi dihapus dari `taggables`, (b) entitas taggable tidak ikut terhapus.
- [x] Unit test untuk constraint user isolation: user A tidak bisa mengakses Tag milik user B (via Policy).
- [x] `php artisan test` seluruh test suite hijau.

## Checklist Sebelum Mulai

- [x] TASK-0005 berstatus `Done` (`tags` dan `taggables` tersedia, Model `Tag` ada).
- [x] `docs/rules/LARAVEL_RULES.md` dibaca — khususnya bagian Action Pattern dan Policy Pattern.
- [x] `docs/rules/TESTING_RULES.md` dibaca — unit test Action wajib sebelum UI.

## Checklist Setelah Selesai

- [x] Semua unit test di atas hijau.
- [x] Policy dipanggil sebelum Action di setiap alur yang melibatkan Tag milik user.
- [x] Commit: `tagging: add TagFactory, TagPolicy, and tag actions with unit tests`.
- [x] Pindahkan ke `DONE.md`.
- [x] Jadikan TASK-0007 sebagai `CURRENT_TASK.md` baru.

## Catatan Teknis

- **`CreateTag` bukan `CreateOrFirstTag`** — namanya tetap `CreateTag` (singkat, jelas), perilaku upsert-by-name cukup didokumentasikan di docblock method.
- **Tidak ada `UpdateTag` di tiket ini** — rename Tag masuk ke EPIC-001 jika ada, atau Future Enhancement (FSD 4.1 tidak menyebut edit nama tag di MVP, hanya create/attach/detach/delete).
- **`DeleteTag`** — Action untuk menghapus Tag (yang memutus seluruh `taggables` via cascade) **tidak dibuat di tiket ini** secara terpisah; hapus Tag cukup lewat `$tag->delete()` yang sudah di-cover cascade migration. Jika perlu Action `DeleteTag` tersendiri, dibuat saat ada use case UI nyata (TASK-0007 atau modul berikutnya).
- Tipe Model taggable di `AttachTag`/`DetachTag` menggunakan type hint `\Illuminate\Database\Eloquent\Model` + interface atau `MorphToMany` contract — hindari hardcode class Task/Project karena modul-modul tersebut belum ada saat tiket ini dikerjakan.
- Namespace Action: `App\Domain\Shared\Actions\` — konsisten dengan struktur domain dari TASK-0001.
