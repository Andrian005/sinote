<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_preferences', function (Blueprint $table) {
            $table->ulid('id')->primary();

            // One-to-one with User — auto-created via UserObserver on registration.
            $table->foreignUlid('user_id')
                ->unique()
                ->constrained('users')
                ->cascadeOnDelete();

            $table->boolean('deadline_reminder_enabled')->default(true);
            $table->boolean('habit_reminder_enabled')->default(true);
            $table->time('habit_reminder_time')->default('20:00');
            $table->boolean('review_ritual_enabled')->default(true);

            $table->timestamps();
            // No soft deletes — row always exists while the user exists (Database Spec A.12).
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_preferences');
    }
};
