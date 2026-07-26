<?php

namespace App\Livewire\Goals;

use App\Domain\Projects\Actions\ArchiveGoal;
use App\Domain\Projects\Actions\UpdateGoalStatus;
use App\Domain\Projects\Enums\GoalStatus;
use App\Domain\Projects\Exceptions\InvalidGoalTransitionException;
use App\Domain\Projects\Models\Goal;
use App\Livewire\Concerns\WithFlashMessage;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class GoalList extends Component
{
    use WithFlashMessage, WithPagination;

    public string $filter = 'active';

    public function mount(string $filter = 'active'): void
    {
        $this->filter = $filter;
    }

    public function getGoalsProperty(): LengthAwarePaginator
    {
        $query = Goal::where('user_id', auth()->id())
            ->withCount('projects')
            ->orderByDesc('created_at');

        match ($this->filter) {
            'completed' => $query->completed(),
            'archived' => $query->archived(),
            default => $query->active(),
        };

        return $query->paginate(10);
    }

    public function updateStatus(string $goalId, string $newStatus): void
    {
        $goal = Goal::find($goalId);

        if (! $goal) {
            return;
        }

        if ($goal->user_id !== auth()->id()) {
            $this->setFlash('Anda tidak memiliki akses untuk melakukan aksi ini.', error: true);

            return;
        }

        try {
            (new UpdateGoalStatus)->execute($goal, GoalStatus::from($newStatus));
            $this->setFlash('Status goal berhasil diperbarui.');
            $this->resetPage();
        } catch (InvalidGoalTransitionException) {
            $this->setFlash('Transisi status tidak valid.', error: true);
        }
    }

    public function archive(string $goalId): void
    {
        $goal = Goal::find($goalId);

        if (! $goal) {
            return;
        }

        if (Gate::denies('archive', $goal)) {
            $this->setFlash('Anda tidak memiliki akses untuk melakukan aksi ini.', error: true);

            return;
        }

        try {
            (new ArchiveGoal(new UpdateGoalStatus))->execute($goal);
            $this->setFlash('Goal berhasil diarsipkan.');
            $this->resetPage();
        } catch (InvalidGoalTransitionException) {
            $this->setFlash('Goal sudah diarsipkan sebelumnya.', error: true);
        }
    }

    #[On('goal-saved')]
    public function refreshList(): void
    {
        $this->resetPage();
        unset($this->goals);
    }

    public function render()
    {
        return view('livewire.goals.goal-list', ['goals' => $this->goals]);
    }
}
