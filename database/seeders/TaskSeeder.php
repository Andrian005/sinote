<?php

namespace Database\Seeders;

use App\Domain\Shared\Models\User;
use App\Domain\Tasks\Models\Task;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();

        if (! $user) {
            $this->command->warn('No users found. Skipping TaskSeeder.');

            return;
        }

        // 5 todo tasks — varied priorities and due dates
        $todos = [
            ['title' => 'Buat outline video YouTube minggu ini', 'priority' => 'high', 'due_date' => now()->addDays(2)->toDateString()],
            ['title' => 'Review PR dari tim untuk fitur login', 'priority' => 'high', 'due_date' => now()->toDateString()],
            ['title' => 'Update dependensi Composer ke versi terbaru', 'priority' => 'medium', 'due_date' => now()->addWeek()->toDateString()],
            ['title' => 'Beli peralatan podcast baru', 'priority' => 'low', 'due_date' => null],
            // Overdue — for visual testing
            ['title' => 'Kirim invoice ke klien bulan lalu', 'priority' => 'high', 'due_date' => now()->subDays(3)->toDateString()],
        ];

        foreach ($todos as $data) {
            Task::factory()->forUser($user)->todo()->withTitle($data['title'])
                ->create(['priority' => $data['priority'], 'due_date' => $data['due_date']]);
        }

        // 3 in_progress tasks
        $inProgress = [
            'Implementasi fitur search di aplikasi',
            'Redesign halaman profil pengguna',
            'Belajar Rust — bab 3 dan 4',
        ];

        foreach ($inProgress as $title) {
            Task::factory()->forUser($user)->inProgress()->withTitle($title)->create();
        }

        // 3 done tasks
        $done = [
            'Setup environment development baru',
            'Tulis unit test untuk AuthService',
            'Meeting dengan desainer soal wireframe',
        ];

        foreach ($done as $title) {
            Task::factory()->forUser($user)->done()->withTitle($title)->create();
        }

        // 2 archived tasks
        $archived = [
            'Cek apakah perlu upgrade server VPS',
            'Riset library PDF generation untuk PHP',
        ];

        foreach ($archived as $title) {
            Task::factory()->forUser($user)->archived()->withTitle($title)->create();
        }

        $this->command->info(sprintf(
            'TaskSeeder: %d todo + %d in_progress + %d done + %d archived untuk user: %s',
            count($todos), count($inProgress), count($done), count($archived),
            $user->email
        ));
    }
}
