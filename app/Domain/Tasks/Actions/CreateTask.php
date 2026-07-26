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
     * Create a new Task. Status is always forced to 'todo' on creation.
     *
     * If project_id is provided, ownership is validated against the projects table.
     * The table-existence check is a graceful guard for the case where the Projects
     * module has not yet been migrated.
     *
     * @throws AuthorizationException if project_id belongs to another user.
     */
    public function execute(User $user, array $data): Task
    {
        $projectId = $data['project_id'] ?? null;

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

        $priority = isset($data['priority'])
            ? TaskPriority::from($data['priority'])
            : TaskPriority::Medium;

        return Task::create([
            'user_id' => $user->id,
            'project_id' => $projectId,
            'title' => trim($data['title'] ?? ''),
            'description' => $data['description'] ?? null,
            'status' => TaskStatus::Todo,
            'priority' => $priority,
            'due_date' => $data['due_date'] ?? null,
            'completed_at' => null,
        ]);
    }

    private function projectsTableExists(): bool
    {
        return DB::getSchemaBuilder()->hasTable('projects');
    }
}
