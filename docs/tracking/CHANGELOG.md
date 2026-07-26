# CHANGELOG.md

> Satu baris ringkas per tiket selesai, terbaru di atas. Diperbarui di sesi yang sama dengan penyelesaian tiket (lihat `CORE_RULES.md` § 8).

## Format

```
## [Unreleased]

- {modul}: {deskripsi singkat} (TICKET-ID)
```

---

## [Unreleased]

- dashboard: add DashboardToday Livewire component, stats bar, today tasks aggregation, 11 feature tests — EPIC-005 complete (TASK-0017)
- dashboard: plan EPIC-005 sprint — create TASK-0017 ticket (FEAT-0005)
- projects: add GoalForm + GoalList + ProjectForm + ProjectList Livewire components, /goals + /projects routes, dashboard widget, nav update, GoalSeeder + ProjectSeeder, 42 feature tests — EPIC-004 complete (TASK-0016)
- projects: add GoalFactory, ProjectFactory, GoalPolicy, ProjectPolicy, form requests, actions, RecalculateProjectProgress, update listener, 69 unit tests (TASK-0015)
- projects: add goals + projects migrations, GoalType/GoalStatus/ProjectStatus enums, Goal + Project models, resolve D-009 FK tasks.project_id (TASK-0014)
- projects: plan EPIC-004 sprint — create TASK-0014, TASK-0015, TASK-0016 tickets (FEAT-0004)
- tasks: add TaskList + TaskForm Livewire components, /tasks route, dashboard widget, nav link, TaskSeeder, and 21 feature tests — EPIC-003 complete (TASK-0013)
- tasks: add TaskFactory, TaskPolicy, form requests, actions (CreateTask/UpdateTask/UpdateTaskStatus/ArchiveTask), TaskCompleted event, CreateTaskFromInbox contract impl, and 54 unit tests (TASK-0012)
- tasks: add tasks migration, TaskStatus + TaskPriority enums, and Task model (TASK-0011)
- tasks: plan EPIC-003 sprint — create TASK-0011, TASK-0012, TASK-0013 tickets (FEAT-0003)
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
