<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Migration Order position 9 — pure polymorphic pivot table (Database Spec A.9).
     * No primary key, no soft delete, no timestamps — pivot rows are transient
     * relational data with no standalone meaning.
     *
     * Cascade on tag_id: deleting a Tag cascades to remove all its taggables rows,
     * leaving the tagged entities (Task, Project, Note, Habit) untouched — FSD 4.1.
     */
    public function up(): void
    {
        Schema::create('taggables', function (Blueprint $table) {
            // FK → tags.id: cascade — if a Tag is deleted, its pivot rows are removed.
            $table->foreignUlid('tag_id')
                ->constrained('tags')
                ->cascadeOnDelete();

            // Polymorphic columns — taggable_id stores ULID of the tagged entity.
            $table->ulid('taggable_id');
            $table->string('taggable_type', 50);

            // Prevent the same tag being attached twice to the same entity.
            $table->unique(['tag_id', 'taggable_id', 'taggable_type']);

            // Index for polymorphic reverse-lookup: "all tags on entity X"
            $table->index(['taggable_type', 'taggable_id']);

            // Index for forward-lookup: "all entities tagged with tag Y"
            $table->index('tag_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taggables');
    }
};
