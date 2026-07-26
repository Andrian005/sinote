<?php

namespace App\Livewire\Projects;

use App\Domain\Projects\Actions\ArchiveProject;
use App\Domain\Projects\Actions\UpdateProjectStatus;
use App\Domain\Projects\Enums\ProjectStatus;
use App\Domain\Projects\Exceptions\InvalidProjectTransitionException;
use App\Domain\Projects\Models\Project;
use App\Livewire\Concerns\WithFlashMessage;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class ProjectList extends Component
{
    use WithFlashMessage, WithPagination;

    public string $filter = 'active';

    public int $limit = 0;

    public function mount(string $filter = 'active', int $limit = 0): void
    {
        $this->filter = $filter;
        $this->limit = $limit;
    }

    public function getProjectsProperty(): LengthAwarePaginator|Collection
    {
        $query = Project::where('user_id', auth()->id())
            ->withCount('tasks')
            ->with('goal:id,title')
            ->orderByDesc('created_at');

        match ($this->filter) {
            'completed' => $query->completed(),
            'archived' => $query->archived(),
            default => $query->active(),
        };

        if ($this->limit > 0) {
            return $query->limit($this->limit)->get();
        }

        return $query->paginate(10);
    }

    public function updateStatus(string $projectId, string $newStatus): void
    {
        $project = Project::find($projectId);

        if (! $project) {
            return;
        }

        if (Gate::denies('update', $project)) {
            $this->setFlash('Anda tidak memiliki akses untuk melakukan aksi ini.', error: true);

            return;
        }

        try {
            (new UpdateProjectStatus)->execute($project, ProjectStatus::from($newStatus));
            $this->setFlash('Status project berhasil diperbarui.');
            $this->resetPage();
        } catch (InvalidProjectTransitionException) {
            $this->setFlash('Transisi status tidak valid.', error: true);
        }
    }

    public function archive(string $projectId): void
    {
        $project = Project::find($projectId);

        if (! $project) {
            return;
        }

        if (Gate::denies('archive', $project)) {
            $this->setFlash('Anda tidak memiliki akses untuk melakukan aksi ini.', error: true);

            return;
        }

        try {
            (new ArchiveProject(new UpdateProjectStatus))->execute($project);
            $this->setFlash('Project berhasil diarsipkan.');
            $this->resetPage();
        } catch (InvalidProjectTransitionException) {
            $this->setFlash('Project sudah diarsipkan sebelumnya.', error: true);
        }
    }

    #[On('project-saved')]
    public function refreshList(): void
    {
        $this->resetPage();
        unset($this->projects);
    }

    public function render()
    {
        return view('livewire.projects.project-list', ['projects' => $this->projects]);
    }
}
