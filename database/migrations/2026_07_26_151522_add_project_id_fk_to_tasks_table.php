<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * D-009 Resolution: add FK constraint tasks.project_id → projects.id.
     *
     * This migration runs after create_projects_table, so the projects table
     * is now available. SQLite (used for testing) does not support adding FK
     * constraints via ALTER TABLE, so we apply this only on PostgreSQL.
     *
     * On delete: SET NULL — deleting a Project does not cascade-delete its Tasks.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement(
                'ALTER TABLE tasks
                 ADD CONSTRAINT tasks_project_id_foreign
                 FOREIGN KEY (project_id) REFERENCES projects (id)
                 ON DELETE SET NULL'
            );
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement(
                'ALTER TABLE tasks
                 DROP CONSTRAINT IF EXISTS tasks_project_id_foreign'
            );
        }
    }
};
