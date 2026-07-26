<?php

namespace Database\Factories\Domain\Shared;

use App\Domain\Shared\Models\Tag;
use App\Domain\Shared\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Tag>
 */
class TagFactory extends Factory
{
    protected $model = Tag::class;

    /**
     * Define the model's default state.
     *
     * Tag names are always stored lowercase. The factory generates lowercase
     * names by default to match business rules enforced by CreateTag Action.
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => strtolower($this->faker->unique()->word()),
        ];
    }

    /**
     * Create a tag with a specific name for a given user.
     * Name will be normalized to lowercase.
     */
    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }

    /**
     * Create a tag with a specific name.
     * Name will be normalized to lowercase.
     */
    public function withName(string $name): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => strtolower($name),
        ]);
    }
}
