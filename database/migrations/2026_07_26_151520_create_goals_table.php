<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Database Spec A.3.
     * Migration order: after tasks table.
     */
    public function up(): void
    {
        Schema::create('goals', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->foreignUlid('user_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->string('title', 255);
            $table->text('description')->nullable();

            // Immutable after creation (FSD 3.1).
            $table->string('goal_type', 20);

            $table->string('status', 20)->default('active');

            // Required when goal_type = 'time_bound' — enforced in Application layer.
            $table->date('target_date')->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'goal_type']);
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement(
                "ALTER TABLE goals ADD CONSTRAINT goals_goal_type_check
                 CHECK (goal_type IN ('time_bound','ongoing'))"
            );
            DB::statement(
                "ALTER TABLE goals ADD CONSTRAINT goals_status_check
                 CHECK (status IN ('active','completed','archived'))"
            );
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('goals');
    }
};
