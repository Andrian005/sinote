<?php

namespace App\Livewire\Projects;

use App\Domain\Projects\Actions\CreateProject;
use App\Domain\Projects\Actions\UpdateProject;
use App\Domain\Projects\Models\Goal;
use App\Domain\Projects\Models\Project;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Throwable;

class ProjectForm extends Component
{
    public ?string $projectId = null;

    #[Validate('required|string|min:1|max:255')]
    public string $title = '';

    #[Validate('nullable|string|max:10000')]
    public ?string $description = null;

    #[Validate('nullable|string')]
    public ?string $goalId = null;

    #[Validate('nullable|date')]
    public ?string $dueDate = null;

    public bool $saved = false;

    public function mount(?string $projectId = null): void
    {
        $this->projectId = $projectId;

        if ($projectId !== null) {
            $project = Project::find($projectId);

            if ($project && $project->user_id === auth()->id()) {
                $this->title = $project->title;
                $this->description = $project->description;
                $this->goalId = $project->goal_id;
                $this->dueDate = $project->due_date?->toDateString();
            }
        }
    }

    /** Goals owned by the current user for the goal select dropdown. */
    public function getUserGoalsProperty()
    {
        return Goal::where('user_id', auth()->id())
            ->active()
            ->orderBy('title')
            ->get(['id', 'title']);
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'title' => $this->title,
            'description' => $this->description,
            'goal_id' => $this->goalId ?: null,
            'due_date' => $this->dueDate ?: null,
        ];

        try {
            if ($this->projectId === null) {
                (new CreateProject)->execute(auth()->user(), $data);
                $this->reset(['title', 'description', 'goalId', 'dueDate']);
            } else {
                $project = Project::find($this->projectId);

                if ($project && $project->user_id === auth()->id()) {
                    (new UpdateProject)->execute($project, $data);
                }
            }

            $this->saved = true;
            $this->dispatch('project-saved');
        } catch (Throwable) {
            $this->addError('title', 'Gagal menyimpan. Silakan coba lagi.');
        }
    }

    public function resetSaved(): void
    {
        $this->saved = false;
    }

    public function render()
    {
        return view('livewire.projects.project-form', [
            'userGoals' => $this->userGoals,
        ]);
    }
}
