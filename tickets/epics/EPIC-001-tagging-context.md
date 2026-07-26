# EPIC-001: Tagging & Context

- **ID:** EPIC-001
- **Judul:** Tagging/Context (Lapisan Metadata Lintas Modul)
- **Deskripsi:** Membangun entitas `Tag` dan relasi polymorphic `taggables` yang akan dipakai seluruh modul fitur berikutnya (Task, Project, Note, Habit). Lihat FSD Modul 4.
- **Dependency:** EPIC-000 (Core Infrastructure).
- **Priority:** Must Have (dinaikkan dari "Should Have" — lihat DECISIONS.md D-004).
- **Estimasi:** 2–3 hari.
- **Status:** `Done`

## Acceptance Criteria

- [x] Migration `tags` dan `taggables` sesuai Database Spec A.9.
- [x] Tag dapat dibuat, dilekatkan, dan dilepas dari entitas dummy via unit test.
- [x] Unique constraint `(user_id, name)` case-insensitive teruji.
- [x] Menghapus Tag memutus seluruh relasi `taggables` terkait (cascade), tanpa memengaruhi entitas taggable itu sendiri.

## Checklist Sebelum Mulai

- [x] EPIC-000 selesai penuh (Acceptance Criteria tercentang).

## Checklist Setelah Selesai

- [x] `docs/tracking/DONE.md` diperbarui.
- [x] EPIC-002 (Inbox) siap dimulai.

## Feature/Task Turunan

EPIC-001 dipecah menjadi 3 TASK (via FEAT-0001):

- **TASK-0005:** Migration `tags` + `taggables` & Model Tag — `Done` (2026-07-26)
- **TASK-0006:** Factory + Policy + Action Tag + Unit Test — `Done` (2026-07-26)
- **TASK-0007:** Livewire Tag Input Component + Feature Test + TagSeeder — `Done` (2026-07-26)

**Total deliverables:**
- 2 migrations (tags posisi 2, taggables posisi 9)
- 1 Model (Tag dengan HasUlids + morphedByMany stubs)
- 1 Factory (TagFactory dengan state methods)
- 1 Policy (TagPolicy dengan view/update/delete)
- 1 Form Request (StoreTagRequest)
- 3 Actions (CreateTag, AttachTag, DetachTag)
- 1 Livewire Component (TagInput dengan autocomplete)
- 1 Seeder (TagSeeder dengan 12 sample tags)
- **32 tests** (20 unit + 10 feature + 2 dari TASK-0005) — semua hijau
- **90 assertions** total

Lapisan Tag sekarang siap digunakan oleh modul Task, Project, Note, dan Habit.
