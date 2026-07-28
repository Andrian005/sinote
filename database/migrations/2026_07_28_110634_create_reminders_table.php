<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reminders', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->foreignUlid('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Polymorphic source — not a real FK (Database Spec A.11, E.2).
            // Covers: Task, Project, Habit, ReviewCycle.
            $table->ulid('remindable_id');
            $table->string('remindable_type', 50);

            $table->string('reminder_type', 20);
            $table->timestamp('scheduled_at');
            $table->string('status', 20)->default('scheduled');
            $table->timestamp('sent_at')->nullable();

            $table->timestamps();
            // No soft deletes — cancelled/skipped are marked via status (Database Spec A.11).

            // Supports scanner job: WHERE status = 'scheduled' AND scheduled_at <= now()
            $table->index(['status', 'scheduled_at']);

            // Supports cancel lookup: find all reminders for a given entity
            $table->index(['remindable_type', 'remindable_id']);

            // Explicit user_id index for ownership filter queries
            $table->index('user_id');
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement(
                "ALTER TABLE reminders ADD CONSTRAINT reminders_reminder_type_check
                 CHECK (reminder_type IN ('deadline','habit_schedule','review_ritual'))"
            );

            DB::statement(
                "ALTER TABLE reminders ADD CONSTRAINT reminders_status_check
                 CHECK (status IN ('scheduled','sent','cancelled','skipped'))"
            );
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('reminders');
    }
};
