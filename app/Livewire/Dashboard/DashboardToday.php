<?php

namespace App\Livewire\Dashboard;

use App\Domain\Inbox\Models\InboxItem;
use App\Domain\Notification\Models\Reminder;
use App\Domain\Projects\Models\Project;
use App\Domain\Tasks\Enums\TaskStatus;
use App\Domain\Tasks\Models\Task;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\On;
use Livewire\Component;

class DashboardToday extends Component
{
    /**
     * Tasks relevant for today (FSD 5.1 — Today Aggregation View).
     *
     * Criteria: owner is current user, status todo/in_progress, due_date <= today OR null,
     * sorted by priority DESC then due_date ASC NULLS LAST, limit 7.
     */
    public function getTodayTasksProperty(): Collection
    {
        return Task::where('user_id', auth()->id())
            ->whereIn('status', [TaskStatus::Todo, TaskStatus::InProgress])
            ->where(function ($query) {
                $query->whereNull('due_date')
                    ->orWhereDate('due_date', '<=', now()->toDateString());
            })
            ->orderByRaw("CASE priority WHEN 'high' THEN 3 WHEN 'medium' THEN 2 ELSE 1 END DESC")
            ->orderByRaw('due_date ASC NULLS LAST')
            ->limit(7)
            ->get();
    }

    /**
     * Dashboard stats bar counts (tasks active, projects active, inbox unprocessed).
     */
    public function getStatsProperty(): array
    {
        $userId = auth()->id();

        return [
            'tasks' => Task::where('user_id', $userId)
                ->whereIn('status', [TaskStatus::Todo, TaskStatus::InProgress])
                ->count(),

            'projects' => Project::where('user_id', $userId)
                ->active()
                ->count(),

            'inbox' => InboxItem::where('user_id', $userId)
                ->unprocessed()
                ->count(),
        ];
    }

    /**
     * Count of pending reminders (scheduled + scheduled_at <= now) for the stats bar.
     */
    public function getRemindersCountProperty(): int
    {
        return Reminder::where('user_id', auth()->id())
            ->pendingDelivery()
            ->count();
    }

    #[On('task-saved')]
    public function refreshOnTaskSaved(): void
    {
        unset($this->todayTasks, $this->stats);
    }

    #[On('project-saved')]
    public function refreshOnProjectSaved(): void
    {
        unset($this->stats);
    }

    #[On('reminder-updated')]
    public function refreshOnReminderUpdated(): void
    {
        unset($this->remindersCount);
    }

    public function render()
    {
        return view('livewire.dashboard.dashboard-today', [
            'todayTasks' => $this->todayTasks,
            'stats' => $this->stats,
            'remindersCount' => $this->remindersCount,
        ]);
    }
}
