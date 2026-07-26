<?php

namespace App\Policies;

use App\Domain\Projects\Enums\ProjectStatus;
use App\Domain\Projects\Models\Project;
use App\Domain\Shared\Models\User;

class ProjectPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Project $project): bool
    {
        return $user->id === $project->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    /** Only active/completed projects can be edited (not archived). */
    public function update(User $user, Project $project): bool
    {
        return $user->id === $project->user_id
            && $project->status !== ProjectStatus::Archived;
    }

    public function delete(User $user, Project $project): bool
    {
        return $user->id === $project->user_id;
    }

    public function archive(User $user, Project $project): bool
    {
        return $user->id === $project->user_id
            && $project->status !== ProjectStatus::Archived;
    }
}
