# TASK-0014: Migrations, Enum, Models — Goal & Project + FK tasks.project_id

- **ID:** TASK-0014
- **Judul:** Migrations goals + projects + FK tasks.project_id, Enum GoalType/GoalStatus/ProjectStatus, Model Goal + Project
- **Deskripsi:** Membuat fondasi data EPIC-004: dua migration baru (goals, projects), satu migration alter (FK tasks.project_id — resolusi D-009), tiga Enum, dan dua Model dengan cast, scope, relasi, dan integrasi HasUlids + SoftDeletes.
- **Dependency:** TASK-0013 (EPIC-003 selesai; tabel tasks sudah ada).
- **Priority:** Must Have — MVP 0 (v0.1).
- **Estimasi:** 0.75 hari.
- **Status:** `Done`
- **Selesai:** 2026-07-26

## Acceptance Criteria

### Migrations
- [x] `create_goals_table` — 10 kolom, FK user_id restrict, index (user_id,status) + (user_id,goal_type), check constraint pgsql
- [x] `create_projects_table` — 11 kolom, FK user_id restrict + goal_id set null, progress unsignedTinyInt default 0, index (user_id,status) + (user_id,due_date), check constraint pgsql
- [x] `add_project_id_fk_to_tasks_table` — D-009 resolution, pgsql only, `tasks_project_id_foreign` → `projects.id` SET NULL
- [x] `php artisan migrate:fresh` → 12 migrations sukses
- [x] Skema diverifikasi via `db:table`: goals (10 col), projects (11 col), tasks `tasks_project_id_foreign` aktif

### Enum
- [x] `GoalType` — `TimeBound = 'time_bound'`, `Ongoing = 'ongoing'`, `label(): string`
- [x] `GoalStatus` — `Active/Completed/Archived`, `allowedTransitions(): array`, `isActive(): bool`
  - Active → Completed, Archived | Completed → Active (reopen) | Archived → final
- [x] `ProjectStatus` — sama dengan GoalStatus (state machine identik per FSD 3.2)

### Models
- [x] `Goal` di `app/Domain/Projects/Models/Goal.php`:
  - HasUlids, SoftDeletes, newFactory(), fillable, casts (goal_type/status/target_date)
  - Relasi: `belongsTo User`, `hasMany Project`
  - Scopes: `active`, `completed`, `archived`, `timeBound`, `ongoing`, `overdue`
- [x] `Project` di `app/Domain/Projects/Models/Project.php`:
  - HasUlids, SoftDeletes, newFactory(), fillable, casts (status/progress/due_date)
  - Relasi: `belongsTo User`, `belongsTo Goal` (withDefault null), `hasMany Task`
  - Scopes: `active`, `completed`, `archived`, `overdue`

### Verifikasi
- [x] `php artisan test` → 189 passed (275 assertions) — semua test lama tetap hijau
- [x] `vendor/bin/pint` → clean (1 style fix di projects migration)

## Checklist Sebelum Mulai

- [x] FEAT-0004 selesai
- [x] Baca Database Spec A.3 + A.4
- [x] Baca Business Rules B.2 + B.3
- [x] Referensi TASK-0011 sebagai pola

## Checklist Setelah Selesai

- [x] `php artisan migrate:fresh` hijau
- [x] `php artisan test` → 189+ tests hijau
- [x] `vendor/bin/pint` clean
- [x] Status tiket → Done
- [x] `DONE.md` + `CHANGELOG.md` diperbarui
- [x] `CURRENT_TASK.md` → TASK-0015

## Catatan Implementasi

**Namespace:** `App\Domain\Projects\` untuk Goals dan Projects — satu domain karena keduanya erat terkait.

**D-009 Resolution:** FK `tasks_project_id_foreign` aktif di PostgreSQL. `CreateTask` guard `projectsTableExists()` otomatis aktif sekarang.

**`Task::project()` relation:** Sebelumnya menggunakan string FQCN `'App\Domain\Projects\Models\Project'` karena Project belum ada. Setelah TASK-0014, relasi ini resolve ke class nyata — bisa diganti ke import langsung di TASK-0015 cleanup jika diperlukan.

**progress field:** Tipe `smallint` (PostgreSQL menyimpan `unsignedTinyInteger` sebagai `int2`/smallint) — rentang 0–100 dijaga oleh check constraint di pgsql dan Application layer.
