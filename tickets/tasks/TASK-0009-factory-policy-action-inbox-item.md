# TASK-0009: InboxItemFactory, Policy, Request, Actions + Unit Tests

- **ID:** TASK-0009
- **Judul:** InboxItemFactory, InboxItemPolicy, StoreInboxItemRequest, Actions Inbox + Unit Tests
- **Deskripsi:** Membuat Factory untuk testing, Policy untuk authorization, Form Request untuk validasi, dan Actions untuk logika bisnis Inbox (Create, Triage, Discard) beserta unit test lengkap sesuai FSD Modul 1.
- **Dependency:** TASK-0008 (Migration & Model InboxItem).
- **Priority:** Must Have — MVP 0 (v0.1).
- **Estimasi:** 1 hari.
- **Status:** `Done`
- **Selesai:** 2026-07-26

## Acceptance Criteria

### Factory
- [x] `InboxItemFactory` dibuat di `database/factories/Domain/Inbox/InboxItemFactory.php` dengan:
  - State method `forUser(User $user)`
  - State method `unprocessed()`, `processed()`, `discarded()` untuk setiap status
  - State method `withContent(string $content)` untuk custom content
  - Default content: faker paragraph (1-2 kalimat, ~100-200 karakter)

### Policy
- [x] `InboxItemPolicy` dibuat di `app/Policies/InboxItemPolicy.php` dengan:
  - `viewAny`: selalu true (filtering dilakukan via user_id scope di query)
  - `view`: user hanya dapat melihat InboxItem miliknya
  - `create`: selalu true (user dapat membuat InboxItem baru)
  - `update`: user hanya dapat mengubah InboxItem miliknya yang berstatus `unprocessed`
  - `delete`: user hanya dapat menghapus InboxItem miliknya
  - `triage`: user hanya dapat mentriage InboxItem miliknya yang berstatus `unprocessed`
- [x] Policy terdaftar di `AuthServiceProvider`
- [x] Unit test `InboxItemPolicyTest` dibuat dengan coverage seluruh method (14 tests)

### Form Request
- [x] `StoreInboxItemRequest` dibuat di `app/Http/Requests/StoreInboxItemRequest.php` dengan validasi:
  - `content`: required, string, min:1, max:5000 (sesuai FSD 1.1)
  - Validasi tambahan: strip whitespace via `prepareForValidation()` sebelum validasi min
- [x] `TriageInboxItemRequest` dibuat dengan validasi:
  - `target_type`: required, in:task,note,project
  - `project_id`: required_if:target_type,project, exists:projects,id
  - Catatan: ownership `project_id` divalidasi di Action, bukan Form Request

### Actions
- [x] `CreateInboxItem` dibuat di `app/Domain/Inbox/Actions/CreateInboxItem.php`:
  - Parameter: `User $user, string $content`
  - Return: `InboxItem`
  - Logika: trim content, buat InboxItem dengan status `unprocessed`

- [x] `TriageInboxItem` dibuat di `app/Domain/Inbox/Actions/TriageInboxItem.php`:
  - Parameter: `User $user, InboxItem $inboxItem, string $targetType`
  - Return: `Model` (entity yang dibuat)
  - Logika: guard status + dispatch via contracts, update InboxItem ke processed
  - Exception: `InboxItemAlreadyProcessedException` + `InvalidArgumentException`
  - **Implementasi:** menggunakan contracts `CreatesTaskFromInbox` + `CreatesNoteFromInbox` di `app/Domain/Inbox/Contracts/` — implementasi konkret disambungkan di EPIC-003/EPIC-005

- [x] `DiscardInboxItem` dibuat di `app/Domain/Inbox/Actions/DiscardInboxItem.php`:
  - Parameter: `InboxItem $inboxItem`
  - Return: `bool`
  - Logika: guard status → `discarded`, set `processed_at`
  - Exception: `InboxItemAlreadyProcessedException`

### Unit Tests
- [x] `CreateInboxItemTest` — 6 tests hijau
- [x] `TriageInboxItemTest` — 13 tests hijau (mock contracts via Mockery)
- [x] `DiscardInboxItemTest` — 6 tests hijau
- [x] `InboxItemPolicyTest` — 14 tests hijau
- [x] Seluruh unit test hijau: `php artisan test` → 91 passed (146 assertions)
- [x] Code style clean: `vendor/bin/pint` → 0 issues

## Checklist Sebelum Mulai

- [x] TASK-0008 selesai (Migration & Model tersedia).
- [x] Baca FSD Modul 1 untuk memahami business rules triase.
- [x] Baca Database Spec A.2 dan Bagian B.6 (Business Rules InboxItem).

## Checklist Setelah Selesai

- [x] Minimal 15-20 unit tests hijau untuk Actions dan Policy — tercapai 39 tests baru.
- [x] Status tiket diubah menjadi `Done`.
- [x] `DONE.md` dan `CHANGELOG.md` diperbarui.

## Catatan Implementasi

- `TriageInboxItem` menggunakan constructor injection dua contracts sebagai ganti memanggil CreateTask/CreateNote langsung. Ini memungkinkan Action sepenuhnya teruji tanpa modul Task/Note yang belum ada.
- Valid `targetType` dibatasi ke `task` dan `note`. Nilai `project` yang tercantum di tiket awal tidak didukung karena FSD Modul 1 hanya mendefinisikan konversi ke Task dan Note — dicatat sebagai keputusan implementasi.
- `InboxItemAlreadyProcessedException` dibuat di `app/Domain/Inbox/Exceptions/` — digunakan bersama oleh `DiscardInboxItem` dan `TriageInboxItem`.
- Bug factory Faker: `paragraph(nb_sentences: 2)` tidak kompatibel dengan versi Faker yang memanggil `call_user_func_array` — diperbaiki ke `paragraph(2)`.
