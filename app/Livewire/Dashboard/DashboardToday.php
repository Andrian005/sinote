<?php

namespace App\Livewire\Dashboard;

use App\Domain\Inbox\Models\InboxItem;
use App\Domain\Projects\Models\Project;
use App\Domain\Tasks\Enums\TaskStatus;
use App\Domain\Tasks\Models\Task;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\On;
use Livewire\Component;

class DashboardToday extends Component
{
    // -------------------------------------------------------------------------
    // Computed: Today's Tasks
    // -------------------------------------------------------------------------

    /**
     * Tasks relevant for today (FSD 5.1 — Today Aggregation View).
     *
     * Criteria:
     *   - Owner: current user
     *   - Status: todo OR in_progress
     *   - Due date: <= today (overdue + today) OR null (no deadline)
     *   - Sort: priority DESC (high→medium→low), then due_date ASC NULLS LAST
     *   - Limit: 7
     *
     * Tasks with no due_date are included but sorted last so urgent tasks
     * with upcoming deadlines appear at the top.
     *
     * @return Collection<int, Task>
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

    // -------------------------------------------------------------------------
    // Computed: Stats Bar
    // -------------------------------------------------------------------------

    /**
     * Dashboard stats bar counts.
     *
     * Three lightweight COUNT queries — no joins needed.
     *
     * @return array{tasks: int, projects: int, inbox: int}
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

    // -------------------------------------------------------------------------
    // Event listeners — refresh on task/project changes
    // -------------------------------------------------------------------------

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

    // -------------------------------------------------------------------------
    // Render
    // -------------------------------------------------------------------------

    public function render()
    {
        return view('livewire.dashboard.dashboard-today', [
            'todayTasks' => $this->todayTasks,
            'stats' => $this->stats,
        ]);
    }
}
