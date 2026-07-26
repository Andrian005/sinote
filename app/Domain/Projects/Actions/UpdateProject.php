<?php

namespace App\Domain\Projects\Actions;

use App\Domain\Projects\Models\Project;

class UpdateProject
{
    /**
     * Update editable attributes of a Project.
     * Strips status and progress — must be changed via dedicated Actions.
     */
    public function execute(Project $project, array $data): Project
    {
        unset($data['status'], $data['progress']);

        if (isset($data['title'])) {
            $data['title'] = trim($data['title']);
        }

        $project->update($data);

        return $project->fresh();
    }
}
