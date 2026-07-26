<?php

namespace Database\Seeders;

use App\Domain\Projects\Models\Goal;
use App\Domain\Projects\Models\Project;
use App\Domain\Shared\Models\User;
use App\Domain\Tasks\Models\Task;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();

        if (! $user) {
            $this->command->warn('No users found. Skipping ProjectSeeder.');

            return;
        }

        $goals = Goal::where('user_id', $user->id)->active()->get();
        $firstGoal = $goals->first();
        $secondGoal = $goals->skip(1)->first();

        // Active project 1 — attached to first goal, 4 tasks (2 done, 2 todo)
        $project1 = Project::factory()->forUser($user)->active()->withTitle('Aplikasi manajemen tugas personal')
            ->create(['goal_id' => $firstGoal?->id, 'progress' => 50]);
        Task::factory()->count(2)->forUser($user)->done()->create(['project_id' => $project1->id]);
        Task::factory()->count(2)->forUser($user)->todo()->create(['project_id' => $project1->id]);

        // Active project 2 — attached to second goal, 3 tasks (1 done, 2 in_progress)
        $project2 = Project::factory()->forUser($user)->active()->withTitle('Program latihan lari 10 minggu')
            ->create(['goal_id' => $secondGoal?->id, 'progress' => 33]);
        Task::factory()->forUser($user)->done()->create(['project_id' => $project2->id]);
        Task::factory()->count(2)->forUser($user)->inProgress()->create(['project_id' => $project2->id]);

        // Active project 3 — standalone (no goal), 3 todo tasks
        $project3 = Project::factory()->forUser($user)->active()->withTitle('Redesign blog pribadi')
            ->create(['progress' => 0]);
        Task::factory()->count(3)->forUser($user)->todo()->create(['project_id' => $project3->id]);

        // 2 completed projects
        Project::factory()->forUser($user)->completed()->withTitle('Setup Laragon + PostgreSQL lokal')->create(['progress' => 100]);
        Project::factory()->forUser($user)->completed()->withTitle('Belajar Livewire 3 basics')->create(['progress' => 100]);

        // 1 archived project
        Project::factory()->forUser($user)->archived()->withTitle('Proyek eksperimen yang tidak dilanjutkan')->create();

        $this->command->info("ProjectSeeder: 6 projects dibuat untuk user: {$user->email}");
    }
}
