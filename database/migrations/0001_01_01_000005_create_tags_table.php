<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Migration Order position 2 (after users) — Database Spec A.9.
     * Tags must exist before taggables (position 9) and before any taggable entity
     * table that references tags via the taggables pivot.
     */
    public function up(): void
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->ulid('id')->primary();

            // FK: cascade on delete — tags are owned directly by the user;
            // if the user is deleted, their tags have no meaning on their own.
            $table->foreignUlid('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Stored as normalised lowercase — actual casing is handled at the
            // Action level (CreateTag) before persistence. See Database Spec A.9.
            $table->string('name', 50);

            $table->timestamps();

            // Unique constraint: one tag name per user (case-insensitive enforced
            // by always storing lowercase — FSD 4.1 Business Rules).
            $table->unique(['user_id', 'name']);

            // Explicit index on user_id for filtering tags by owner
            // (FK index alone is not guaranteed across all DB engines — DATABASE_RULES.md).
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tags');
    }
};
