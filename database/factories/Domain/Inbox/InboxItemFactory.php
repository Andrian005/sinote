<?php

namespace Database\Factories\Domain\Inbox;

use App\Domain\Inbox\Enums\InboxItemStatus;
use App\Domain\Inbox\Models\InboxItem;
use App\Domain\Shared\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InboxItem>
 */
class InboxItemFactory extends Factory
{
    protected $model = InboxItem::class;

    /**
     * Define the model's default state.
     *
     * Default status is 'unprocessed' — the entry point of every InboxItem.
     * Content is a short faker paragraph capped at ~200 characters.
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'content' => $this->faker->paragraph(2),
            'status' => InboxItemStatus::Unprocessed,
            'converted_to_type' => null,
            'converted_to_id' => null,
            'processed_at' => null,
        ];
    }

    /**
     * Associate the InboxItem with a specific user.
     */
    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }

    /**
     * Set status to unprocessed (default, but explicit for test clarity).
     */
    public function unprocessed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => InboxItemStatus::Unprocessed,
            'processed_at' => null,
        ]);
    }

    /**
     * Set status to processed, simulating a triaged item.
     */
    public function processed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => InboxItemStatus::Processed,
            'processed_at' => now(),
        ]);
    }

    /**
     * Set status to discarded.
     */
    public function discarded(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => InboxItemStatus::Discarded,
            'processed_at' => now(),
        ]);
    }

    /**
     * Override content with a specific string.
     */
    public function withContent(string $content): static
    {
        return $this->state(fn (array $attributes) => [
            'content' => $content,
        ]);
    }
}
