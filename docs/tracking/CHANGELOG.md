# CHANGELOG.md

> Satu baris ringkas per tiket selesai, terbaru di atas. Diperbarui di sesi yang sama dengan penyelesaian tiket (lihat `CORE_RULES.md` § 8).

## Format

```
## [Unreleased]

- {modul}: {deskripsi singkat} (TICKET-ID)
```

---

## [Unreleased]

- inbox: add QuickCapture + InboxList Livewire components, /inbox route, dashboard widget, nav link, InboxItemSeeder, and 24 feature tests — EPIC-002 complete (TASK-0010)
- inbox: add InboxItemFactory, InboxItemPolicy, form requests, actions (CreateInboxItem/DiscardInboxItem/TriageInboxItem), and 39 unit tests (TASK-0009)
- inbox: add inbox_items migration, InboxItemStatus enum, and InboxItem model (TASK-0008)
- inbox: plan EPIC-002 sprint — create TASK-0008, TASK-0009, TASK-0010 tickets (FEAT-0002)
- tagging: add TagInput Livewire component with autocomplete, feature tests, and TagSeeder (TASK-0007)
- tagging: complete EPIC-001 — Tag layer ready for Task/Project/Note/Habit modules (EPIC-001)
- tagging: add TagFactory, TagPolicy, StoreTagRequest, and tag actions with unit tests (TASK-0006)
- tagging: add tags and taggables migrations and Tag model (TASK-0005)
- tagging: plan EPIC-001 sprint — create TASK-0005, TASK-0006, TASK-0007 tickets (FEAT-0001)
- core: establish base policy pattern with ExamplePolicy template and unit tests (TASK-0004)
- core: add authentication via breeze/fortify with login/register/logout (TASK-0003)
- core: add ULID users migration and model (TASK-0002)
- core: initialize Laravel development environment (TASK-0001)
