<?php

namespace App\Domain\Projects\Actions;

use App\Domain\Projects\Enums\ProjectStatus;
use App\Domain\Projects\Models\Project;

class ArchiveProject
{
    public function __construct(private readonly UpdateProjectStatus $updateStatus) {}

    public function execute(Project $project): Project
    {
        return $this->updateStatus->execute($project, ProjectStatus::Archived);
    }
}
