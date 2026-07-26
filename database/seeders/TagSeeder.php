<?php

namespace Database\Seeders;

use App\Domain\Shared\Models\Tag;
use App\Domain\Shared\Models\User;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Seed sample tags for development.
     *
     * This seeder creates common tags for the first user in the database.
     * It should only be run in local/development environments — not production.
     */
    public function run(): void
    {
        // Get the first user (or skip if no users exist)
        $user = User::first();

        if (! $user) {
            $this->command->warn('No users found. Skipping TagSeeder.');

            return;
        }

        $sampleTags = [
            'urgent',
            'work',
            'personal',
            'learning',
            'youtube',
            'belajar-jepang',
            'desain',
            'fotografi',
            'coding',
            'idea',
            'later',
            'archived',
        ];

        foreach ($sampleTags as $tagName) {
            Tag::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'name' => strtolower($tagName),
                ],
                [
                    'user_id' => $user->id,
                    'name' => strtolower($tagName),
                ]
            );
        }

        $this->command->info('Sample tags created for user: '.$user->email);
    }
}
