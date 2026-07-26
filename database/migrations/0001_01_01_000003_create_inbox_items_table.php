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
     * Migration Order: after users (position 1), before tags (position 2).
     * InboxItem has no dependencies on other entities except users.
     * Database Spec A.2.
     */
    public function up(): void
    {
        Schema::create('inbox_items', function (Blueprint $table) {
            $table->ulid('id')->primary();

            // FK: restrict on delete — user deletion is restricted at MVP
            // (DATABASE_RULES.md cascade rules).
            $table->foreignUlid('user_id')
                ->constrained('users')
                ->restrictOnDelete();

            // Capture content — quick thoughts, ideas, todos (FSD 1.1).
            $table->text('content');

            // Lifecycle status: unprocessed (default) → processed/discarded.
            $table->string('status', 20)->default('unprocessed');

            // Informational fields: what was this converted into?
            // NOT foreign keys (Database Spec Bagian E, poin 2) — allows
            // deletion of target entity without breaking InboxItem history.
            $table->string('converted_to_type', 30)->nullable();
            $table->ulid('converted_to_id')->nullable();

            // Timestamp when status changed to processed/discarded (FSD 1.2).
            $table->timestamp('processed_at')->nullable();

            // Soft delete (DATABASE_RULES.md #3).
            $table->softDeletes();

            $table->timestamps();

            // Composite index for filtering by owner + status (Database Spec Bagian J).
            $table->index(['user_id', 'status']);
        });

        // Check constraint: status must be one of the three valid values.
        // Only add for PostgreSQL — SQLite doesn't support named CHECK constraints via ALTER TABLE.
        if (DB::getDriverName() === 'pgsql') {
            DB::statement(
                "ALTER TABLE inbox_items ADD CONSTRAINT inbox_items_status_check 
                 CHECK (status IN ('unprocessed', 'processed', 'discarded'))"
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inbox_items');
    }
};
