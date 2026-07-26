# TASK-0007: Livewire Tag Input Component + Feature Test + TagSeeder

- **ID:** TASK-0007
- **Judul:** Livewire Tag Input Component + Feature Test + TagSeeder
- **Deskripsi:** Membangun UI minimal untuk Tag: komponen Livewire tag input (autocomplete + create-on-type), feature test end-to-end alur attach/detach tag, dan TagSeeder untuk data development. Ini adalah tiket penutup EPIC-001 — setelah tiket ini selesai, lapisan Tag siap diintegrasikan oleh modul berikutnya (Inbox, Tasks, dst). Mengikuti Coding Order: Livewire Component + Blade View → Feature Test → Seeder.
- **Dependency:** TASK-0006 (Action `CreateTag`, `AttachTag`, `DetachTag` lolos unit test).
- **Priority:** Must Have (minimal fungsional — autocomplete + attach + detach; styling dapat disempurnakan di tiket selanjutnya).
- **Estimasi:** 1–1.5 hari.
- **Status:** `Done`
- **EPIC Induk:** EPIC-001 — Tagging & Context

## Acceptance Criteria

- [x] Livewire component `TagInput` dibuat di `app/Livewire/Shared/TagInput.php` + view `resources/views/livewire/shared/tag-input.blade.php`.
- [x] `TagInput` menerima prop: `taggableType` (string) dan `taggableId` (string ULID) — mengidentifikasi entitas yang di-tag.
- [x] `TagInput` menampilkan daftar tag yang sudah terpasang pada entitas, dan menyediakan input autocomplete untuk mencari/membuat tag baru.
- [x] Autocomplete menyaring tag milik user aktif berdasarkan input ketikan (case-insensitive, minimum 1 karakter).
- [x] Memilih tag dari autocomplete → memanggil `AttachTag` Action; input kosong setelah berhasil.
- [x] Mengetik nama baru + enter → memanggil `CreateTag` (lowercase, skip jika sudah ada) lalu `AttachTag`; tidak ada duplikat.
- [x] Menekan tombol hapus (×) pada tag yang sudah terpasang → memanggil `DetachTag` Action.
- [x] Seluruh operasi (attach/detach/create) memanggil Policy sebelum Action — user tidak bisa memodifikasi tag milik user lain.
- [x] Feature test (`tests/Feature/Tagging/TagInputTest.php`) mencakup: (a) user dapat attach tag ke entitas dummy, (b) user dapat detach tag, (c) autocomplete hanya menampilkan tag milik user sendiri, (d) create-on-type membuat tag baru dengan nama lowercase.
- [x] `TagSeeder` dibuat di `database/seeders/TagSeeder.php` — menyisipkan beberapa tag umum (mis. "youtube", "belajar-jepang", "desain", "fotografi") untuk user pertama dalam database development; **tidak** dijalankan di production seeder.
- [x] `php artisan test` seluruh test suite hijau (unit + feature).

## Catatan Khusus: Entitas Taggable Dummy

Feature test menggunakan `tests/Fixtures/FakeTaggable.php` (re-using tabel `users` yang selalu ada di test DB) untuk membuktikan alur polymorphic tagging bekerja. Component siap digunakan oleh modul Task/Project/Note/Habit begitu tersedia.

## Checklist Sebelum Mulai

- [x] TASK-0006 berstatus `Done` (Action `CreateTag`, `AttachTag`, `DetachTag` ada dan unit test hijau).
- [x] `docs/rules/UI_RULES.md` dibaca — konvensi Livewire component.
- [x] `docs/context/reference/05-uiux-design-system.md` dibaca (bagian Tag input pattern jika ada).

## Checklist Setelah Selesai

- [x] `php artisan test` hijau penuh.
- [x] `TagInput` dapat di-embed di Blade view lain dengan `<livewire:shared.tag-input :taggableType="..." :taggableId="..." />`.
- [x] `TagSeeder` berjalan tanpa error: `php artisan db:seed --class=TagSeeder`.
- [ ] Commit: `tagging: add TagInput Livewire component, feature test, and TagSeeder`.
- [x] Status EPIC-001 diubah menjadi `Done` di `tickets/epics/EPIC-001-tagging-context.md`.
- [x] Pindahkan ke `DONE.md`.
- [x] Jadikan FEAT-0002 atau tiket kickoff EPIC-002 (Inbox) sebagai `CURRENT_TASK.md` baru.

## Catatan Teknis

- **Komponen ini bersifat "dumb UI"** — tidak menyimpan state bisnis sendiri, hanya meneruskan ke Action. Logika normalisasi nama (lowercase) tetap berada di `CreateTag` Action, bukan di komponen.
- **Sorting autocomplete:** tag diurutkan berdasarkan frekuensi pemakaian (FSD 4.1 Sorting) — implementasi awal cukup dengan `ORDER BY (SELECT COUNT(*) FROM taggables WHERE tag_id = tags.id) DESC`, dioptimasi jika perlu di sprint mendatang.
- **Styling:** cukup fungsional (Tailwind utility dasar) — desain detail mengikuti sprint UI/polish, bukan di tiket ini.
- Namespace Livewire: `App\Livewire\Shared\TagInput` — konsisten dengan `App\Livewire\Pages\Auth\*` yang sudah ada dari TASK-0003.
