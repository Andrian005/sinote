<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Migration Order: after inbox_items (position 3) and tags (position 5-6).
     * Database Spec A.5.
     *
     * Note: project_id is stored without a FK constraint here because the
     * `projects` table does not exist yet (EPIC-004). The column is indexed
     * for query performance. The FK constraint will be added via an ALTER
     * TABLE migration in EPIC-004 once `projects` is created.
     * Decision recorded in DECISIONS.md.
     */
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->ulid('id')->primary();

            // Owner — restrict on delete (Database Spec cascade rules).
            $table->foreignUlid('user_id')
                ->constrained('users')
                ->restrictOnDelete();

            // Optional Project association (FK constraint added in EPIC-004).
            // Using plain ulid column + index instead of foreignUlid()->constrained()
            // because `projects` table does not yet exist.
            $table->ulid('project_id')->nullable()->index();

            // Core fields (FSD 2.1).
            $table->string('title', 255);
            $table->text('description')->nullable();

            // Status lifecycle: todo (default) → in_progress → done/archived.
            $table->string('status', 20)->default('todo');

            // Priority: low | medium (default) | high.
            $table->string('priority', 10)->default('medium');

            // Due date — may be in the past for retroactive recording (FSD 2.1).
            $table->date('due_date')->nullable();

            // Set automatically when status transitions to 'done' (FSD 2.2).
            $table->timestamp('completed_at')->nullable();

            // Soft delete (Database Spec global strategy).
            $table->softDeletes();

            $table->timestamps();

            // Composite index for Dashboard queries: user tasks by status + due date.
            // Covers: WHERE user_id = ? AND status IN (?) ORDER BY due_date ASC
            // (TDD bagian 28, Database Spec A.5).
            $table->index(['user_id', 'status', 'due_date']);
        });

        // Check constraints — PostgreSQL only (SQLite used for testing does not
        // support named CHECK constraints via ALTER TABLE).
        if (DB::getDriverName() === 'pgsql') {
            DB::statement(
                "ALTER TABLE tasks ADD CONSTRAINT tasks_status_check
                 CHECK (status IN ('todo','in_progress','done','archived'))"
            );

            DB::statement(
                "ALTER TABLE tasks ADD CONSTRAINT tasks_priority_check
                 CHECK (priority IN ('low','medium','high'))"
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
