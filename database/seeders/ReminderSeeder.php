<?php

namespace Database\Seeders;

use App\Domain\Notification\Models\Reminder;
use App\Domain\Shared\Models\User;
use App\Domain\Tasks\Models\Task;
use Illuminate\Database\Seeder;

class ReminderSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();

        if (! $user) {
            $this->command->warn('No users found. Skipping ReminderSeeder.');

            return;
        }

        // Use the first available task belonging to this user as the remindable entity.
        // Falls back to creating a new task if none exist yet.
        $task = Task::where('user_id', $user->id)->first()
            ?? Task::factory()->forUser($user)->todo()
                ->withTitle('Task contoh untuk reminder')
                ->create();

        // 3 scheduled reminders (pending delivery or upcoming)
        // 1. Due today (H) — sudah tiba, harus muncul di dashboard
        Reminder::factory()->forUser($user)->forTask($task)->scheduled()
            ->create([
                'scheduled_at' => now()->setTime(8, 0),
            ]);

        // 2. Due tomorrow (H-1) — belum tiba, tidak muncul di widget
        Reminder::factory()->forUser($user)->forTask($task)->scheduled()
            ->create([
                'scheduled_at' => now()->addDay()->setTime(8, 0),
            ]);

        // 3. Past due but not yet sent — sudah melewati waktu, muncul di dashboard
        Reminder::factory()->forUser($user)->forTask($task)->scheduled()
            ->create([
                'scheduled_at' => now()->subHours(2),
            ]);

        // 2 sent reminders (historical records — tidak muncul di widget)
        Reminder::factory()->count(2)->forUser($user)->forTask($task)->sent()
            ->create([
                'scheduled_at' => now()->subDays(3)->setTime(8, 0),
            ]);

        $this->command->info(sprintf(
            'ReminderSeeder: 3 scheduled (2 pending delivery, 1 upcoming) + 2 sent untuk user: %s',
            $user->email
        ));
    }
}
