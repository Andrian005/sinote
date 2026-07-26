<?php

namespace App\Domain\Projects\Actions;

use App\Domain\Projects\Enums\ProjectStatus;
use App\Domain\Projects\Models\Goal;
use App\Domain\Projects\Models\Project;
use App\Domain\Shared\Models\User;
use Illuminate\Auth\Access\AuthorizationException;

class CreateProject
{
    /**
     * Create a new Project for the given user.
     *
     * If goal_id is provided, validates that the Goal belongs to the same user.
     * Default status: active, progress: 0.
     *
     * @param  array{title: string, description?: string|null, goal_id?: string|null, due_date?: string|null}  $data
     *
     * @throws AuthorizationException if goal_id belongs to another user
     */
    public function execute(User $user, array $data): Project
    {
        $goalId = $data['goal_id'] ?? null;

        if ($goalId !== null) {
            $owned = Goal::where('id', $goalId)
                ->where('user_id', $user->id)
                ->whereNull('deleted_at')
                ->exists();

            if (! $owned) {
                throw new AuthorizationException(
                    'The selected goal does not belong to the authenticated user.'
                );
            }
        }

        return Project::create([
            'user_id' => $user->id,
            'goal_id' => $goalId,
            'title' => trim($data['title']),
            'description' => $data['description'] ?? null,
            'status' => ProjectStatus::Active,
            'progress' => 0,
            'due_date' => $data['due_date'] ?? null,
        ]);
    }
}
