<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     *
     * Urutan seeder mengikuti dependency:
     * 1. UserSeeder (fondasi - dibutuhkan semua seeder lain)
     * 2. TagSeeder (independen, hanya butuh user)
     * 3. InboxItemSeeder (butuh user)
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            TagSeeder::class,
            InboxItemSeeder::class,
            TaskSeeder::class,
            GoalSeeder::class,
            ProjectSeeder::class,
        ]);
    }
}
