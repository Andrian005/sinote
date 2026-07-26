<?php

namespace App\Domain\Tasks\Actions;

use App\Domain\Shared\Models\User;
use App\Domain\Tasks\Enums\TaskPriority;
use App\Domain\Tasks\Enums\TaskStatus;
use App\Domain\Tasks\Models\Task;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;

class CreateTask
{
    /**
     * Create a new Task for the given user.
     *
     * Title is trimmed before persisting. Status is always forced to 'todo'
     * on creation — callers cannot override this to maintain lifecycle integrity.
     *
     * If project_id is provided, ownership is validated against the projects
     * table when it exists. While EPIC-004 is pending, the guard is skipped
     * gracefully (table does not exist yet — see D-009 / DECISIONS.md).
     *
     * @param  array{title: string, description?: string|null, priority?: string|null, due_date?: string|null, project_id?: string|null}  $data
     *
     * @throws AuthorizationException if project_id belongs to another user
     */
    public function execute(User $user, array $data): Task
    {
        $title = trim($data['title'] ?? '');
        $projectId = $data['project_id'] ?? null;

        // Validate project ownership when projects table exists (EPIC-004).
        if ($projectId !== null && $this->projectsTableExists()) {
            $owned = DB::table('projects')
                ->where('id', $projectId)
                ->where('user_id', $user->id)
                ->whereNull('deleted_at')
                ->exists();

            if (! $owned) {
                throw new AuthorizationException(
                    'The selected project does not belong to the authenticated user.'
                );
            }
        }

        $priority = isset($data['priority']) && $data['priority'] !== null
            ? TaskPriority::from($data['priority'])
            : TaskPriority::Medium;

        return Task::create([
            'user_id' => $user->id,
            'project_id' => $projectId,
            'title' => $title,
            'description' => $data['description'] ?? null,
            'status' => TaskStatus::Todo,
            'priority' => $priority,
            'due_date' => $data['due_date'] ?? null,
            'completed_at' => null,
        ]);
    }

    /** Graceful check — projects table may not exist during EPIC-003. */
    private function projectsTableExists(): bool
    {
        return DB::getSchemaBuilder()->hasTable('projects');
    }
}
