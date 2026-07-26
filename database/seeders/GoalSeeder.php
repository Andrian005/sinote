<?php

namespace Database\Seeders;

use App\Domain\Projects\Models\Goal;
use App\Domain\Shared\Models\User;
use Illuminate\Database\Seeder;

class GoalSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();

        if (! $user) {
            $this->command->warn('No users found. Skipping GoalSeeder.');

            return;
        }

        // 2 active time_bound (1 overdue)
        Goal::factory()->forUser($user)->timeBound(now()->addMonths(2)->toDateString())
            ->withTitle('Lari 10K sebelum akhir tahun')->create();
        Goal::factory()->forUser($user)->overdue()
            ->withTitle('Selesaikan kursus Laravel online')->create();

        // 2 active ongoing
        Goal::factory()->forUser($user)->ongoing()
            ->withTitle('Bangun kebiasaan baca 30 menit per hari')->create();
        Goal::factory()->forUser($user)->ongoing()
            ->withTitle('Rutin journaling setiap malam')->create();

        // 1 completed
        Goal::factory()->forUser($user)->completed()
            ->withTitle('Setup environment development proyek SINOTE')->create();

        // 1 archived
        Goal::factory()->forUser($user)->archived()
            ->withTitle('Coba framework baru yang tidak dipakai lagi')->create();

        $this->command->info("GoalSeeder: 6 goals dibuat untuk user: {$user->email}");
    }
}
