# TASK-0010: Livewire Inbox Components, Feature Tests, Seeder

- **ID:** TASK-0010
- **Judul:** Livewire Quick Capture, Inbox Triage Components, Feature Tests, Seeder
- **Deskripsi:** Membuat Livewire component untuk Quick Capture (FSD 1.1) dan Inbox Triage (FSD 1.2), feature test end-to-end untuk kedua fitur, dan seeder untuk data development. Ini adalah tiket UI akhir yang menyelesaikan EPIC-002.
- **Dependency:** TASK-0009 (Actions Inbox tersedia).
- **Priority:** Must Have — MVP 0 (v0.1).
- **Estimasi:** 1 hari.
- **Status:** `Done`
- **Selesai:** 2026-07-26

## Acceptance Criteria

### Livewire Components

#### Quick Capture Component
- [x] `QuickCapture` dibuat di `app/Livewire/Inbox/QuickCapture.php`:
  - Property public `$content = ''`
  - Method `save()` memanggil `CreateInboxItem` Action
  - Validasi via `#[Validate]` attribute (required, min:1, max:5000)
  - Property `$saved = false` + method `resetSaved()` untuk flash Alpine
  - try/catch: content dipertahankan jika gagal save (FSD 1.1)
- [x] View `resources/views/livewire/inbox/quick-capture.blade.php`:
  - Textarea (rows=4, placeholder, maxlength=5000)
  - Character counter live (`strlen($content)/5000`)
  - Tombol "Simpan" dengan loading state
  - Error validasi via `@error`
  - Flash sukses hijau dengan x-transition fade (3 detik via Alpine)
  - Accessible: aria-label, aria-describedby, role=status, aria-live

#### Inbox Triage Component
- [x] `InboxList` dibuat di `app/Livewire/Inbox/InboxList.php`:
  - Computed `getInboxItemsProperty()` — unprocessed scope + orderByDesc + paginate(10)
  - Method `triage(string $inboxItemId, string $targetType)` dengan Gate check
  - Method `discard(string $inboxItemId)` dengan Gate check
  - Flash via `$flash`/`$flashIsError` + `clearFlash()` (Alpine timer 3 detik)
  - `app(TriageInboxItem::class)` untuk constructor injection contracts
- [x] View `resources/views/livewire/inbox/inbox-list.blade.php`:
  - List item dengan `Str::limit(200)` dan `diffForHumans()`
  - Alpine dropdown per item: Jadikan Task / Jadikan Note / Hapus
  - Empty state: "Inbox kosong — semua sudah tertata!"
  - Pagination via `$inboxItems->links()`
  - Accessible: role=list, role=menu, aria-expanded, aria-haspopup

### Route & Halaman
- [x] `GET /inbox` → name=`inbox.index` → view `livewire.pages.inbox.index`
- [x] Halaman inbox embed QuickCapture + InboxList dalam dua card terpisah

### Integration
- [x] QuickCapture di-embed di `resources/views/dashboard.blade.php` sebagai widget tetap
- [x] Navigation "Dashboard" → "Today", tambah link "Inbox" (desktop + mobile responsive)

### Feature Tests
- [x] `QuickCaptureTest` (9 tests): create item, status unprocessed, form reset, flash saved, validasi kosong/spasi/max, batas 5000 char, isolasi user
- [x] `InboxTriageTest` (15 tests): list visibility, isolasi user, processed tidak muncul, urutan terbaru, discard + flash, discard user lain + flash error, triage task/note + flash, triage user lain + flash error, pagination
- [x] 114 total tests hijau (183 assertions), pint clean

### Seeder
- [x] `InboxItemSeeder`: 7 unprocessed + 3 processed untuk user pertama; skip jika tidak ada user
- [x] Terdaftar di `DatabaseSeeder`

## Checklist Setelah Selesai

- [x] Minimal 15+ feature tests hijau — tercapai 24 tests (9+15).
- [x] Status tiket diubah menjadi `Done`.
- [x] `DONE.md` dan `CHANGELOG.md` diperbarui.
- [x] EPIC-002 ditandai `Done`.

## Catatan Implementasi

- `InboxList::triage()` menggunakan `app(TriageInboxItem::class)` agar contracts bisa di-resolve dari container — memungkinkan feature test meng-inject mock tanpa `new TriageInboxItem(...)`.
- `bindTriageMocks()` helper function di `InboxTriageTest.php` mendaftarkan mock `CreatesTaskFromInbox` + `CreatesNoteFromInbox` ke container Laravel — sehingga triage tests berjalan tanpa EPIC-003/005.
- DatabaseSeeder sebelumnya kehilangan `class DatabaseSeeder extends Seeder` akibat kesalahan str_replace — diperbaiki dengan menulis ulang file lengkap.
