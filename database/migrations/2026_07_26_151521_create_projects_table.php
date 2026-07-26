<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Database Spec A.4.
     * Migration order: after goals table.
     */
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->foreignUlid('user_id')
                ->constrained('users')
                ->restrictOnDelete();

            // Optional Goal association — set null if Goal is deleted.
            $table->foreignUlid('goal_id')
                ->nullable()
                ->constrained('goals')
                ->nullOnDelete();

            $table->string('title', 255);
            $table->text('description')->nullable();

            $table->string('status', 20)->default('active');

            // Calculated automatically by RecalculateProjectProgress Action (0–100).
            $table->unsignedTinyInteger('progress')->default(0);

            $table->date('due_date')->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'due_date']);
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement(
                "ALTER TABLE projects ADD CONSTRAINT projects_status_check
                 CHECK (status IN ('active','completed','archived'))"
            );
            DB::statement(
                'ALTER TABLE projects ADD CONSTRAINT projects_progress_check
                 CHECK (progress BETWEEN 0 AND 100)'
            );
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
