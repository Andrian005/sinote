# TASK-0011: Migration `tasks`, Enum TaskStatus + TaskPriority, Model Task

- **ID:** TASK-0011
- **Judul:** Migration `tasks`, Enum TaskStatus + TaskPriority, Model Task
- **Deskripsi:** Membuat migration tabel `tasks` sesuai Database Spec A.5, dua Enum (TaskStatus dan TaskPriority), dan Model Task dengan cast, scope, relasi, dan integrasi HasUlids + SoftDeletes. Ini adalah fondasi EPIC-003 — Tasks.
- **Dependency:** TASK-0010 (EPIC-002 selesai; tabel `projects` belum ada tapi project_id nullable — migration tetap bisa dibuat dengan deferred FK atau conditional).
- **Priority:** Must Have — MVP 0 (v0.1).
- **Estimasi:** 0.5 hari.
- **Status:** `Done`
- **Selesai:** 2026-07-26

## Acceptance Criteria

### Migration
- [x] Migration `tasks` dibuat di `database/migrations/2026_07_26_134956_create_tasks_table.php`:
  - `id` char(26) ULID, Primary Key
  - `user_id` char(26) ULID, FK → `users.id` (restrict on delete)
  - `project_id` char(26) ULID, nullable, **index saja** (FK ke `projects.id` ditambahkan di EPIC-004 — lihat D-009)
  - `title` varchar(255), not null
  - `description` text, nullable
  - `status` varchar(20), not null, default `'todo'`
  - `priority` varchar(10), not null, default `'medium'`
  - `due_date` date, nullable (boleh masa lalu — FSD 2.1)
  - `completed_at` timestamp, nullable (diisi otomatis saat status → `done`)
  - `deleted_at` timestamp, nullable (soft delete)
  - `created_at` / `updated_at` timestamps
- [x] **Index:** composite `(user_id, status, due_date)`, `(project_id)`
- [x] **Check Constraint** (PostgreSQL only, conditional): `status IN ('todo','in_progress','done','archived')`, `priority IN ('low','medium','high')`
- [x] `php artisan migrate:fresh` berhasil tanpa error — 9 migrations
- [x] Skema diverifikasi via `php artisan db:table tasks` — 12 kolom, 3 index, 1 FK

### Enum
- [x] `TaskStatus` dibuat di `app/Domain/Tasks/Enums/TaskStatus.php`:
  - `Todo = 'todo'`, `InProgress = 'in_progress'`, `Done = 'done'`, `Archived = 'archived'`
  - Method tambahan: `allowedTransitions(): array` (state machine guard siap dipakai TASK-0012)
  - Method tambahan: `isActive(): bool`
- [x] `TaskPriority` dibuat di `app/Domain/Tasks/Enums/TaskPriority.php`:
  - `Low = 'low'`, `Medium = 'medium'`, `High = 'high'`
  - Method tambahan: `weight(): int`, `badgeClass(): string`, `label(): string`

### Model
- [x] `Task` dibuat di `app/Domain/Tasks/Models/Task.php`:
  - `use HasFactory, HasUlids, SoftDeletes`
  - `newFactory()` override → `TaskFactory::new()`
  - `$fillable`: `['user_id', 'project_id', 'title', 'description', 'status', 'priority', 'due_date', 'completed_at']`
  - `casts()`: `status → TaskStatus`, `priority → TaskPriority`, `due_date → 'date'`, `completed_at → 'datetime'`, `deleted_at → 'datetime'`
  - Relasi: `belongsTo User`, `belongsTo Project` (string FQCN + `withDefault(null)`), `morphToMany Tag`
  - Scopes: `todo`, `inProgress`, `done`, `archived`, `active` (todo+in_progress), `pending` (active+due_date), `overdue` (pending+past due_date)
- [x] `php artisan migrate:fresh` + `php artisan db:table tasks` bersih
- [x] `php artisan test` → 114 passed (183 assertions) — seluruh test lama tetap hijau

## Checklist Sebelum Mulai

- [x] FEAT-0003 selesai (tiket-tiket sudah dibuat).
- [x] Baca Database Spec A.5 tabel `tasks` lengkap.
- [x] Baca Database Spec A.4 tabel `projects` untuk memahami FK dependency.
- [x] Baca DEVELOPMENT_PLAYBOOK.md § 5 — posisi saat ini: Migration → Enum → Model.

## Checklist Setelah Selesai

- [x] `php artisan migrate:fresh` hijau.
- [x] `php artisan test` — seluruh test lama (114) tetap hijau.
- [x] `vendor/bin/pint` clean — 93 files.
- [x] Status tiket diubah menjadi `Done`.
- [x] `DONE.md` dan `CHANGELOG.md` diperbarui.
- [x] `CURRENT_TASK.md` diperbarui ke TASK-0012.

## Catatan Implementasi

**FK project_id (D-009):** Kolom `project_id` dibuat sebagai plain ULID dengan `->index()` saja — constraint FK ke `projects.id` ditambahkan via ALTER TABLE migration di EPIC-004. Keputusan ini dicatat di `docs/decisions/DECISIONS.md` sebagai D-009.

**`allowedTransitions()` di Enum:** State machine diimplementasikan langsung di `TaskStatus` Enum — TASK-0012 tinggal memanggil `$currentStatus->allowedTransitions()` di `UpdateTaskStatus` Action tanpa menulis ulang logika transisi.

**`TaskPriority` helper methods:** `badgeClass()` dan `label()` sudah tersedia — TASK-0013 (Livewire + Blade) bisa memanggilnya langsung di view.
