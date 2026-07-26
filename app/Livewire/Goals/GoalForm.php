<?php

namespace App\Livewire\Goals;

use App\Domain\Projects\Actions\CreateGoal;
use App\Domain\Projects\Actions\UpdateGoal;
use App\Domain\Projects\Models\Goal;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Throwable;

class GoalForm extends Component
{
    public ?string $goalId = null;

    /** Readonly in edit mode — goal_type is immutable after creation (FSD 3.1). */
    public bool $isEditMode = false;

    #[Validate('required|string|min:1|max:255')]
    public string $title = '';

    #[Validate('nullable|string|max:10000')]
    public ?string $description = null;

    #[Validate('required|in:time_bound,ongoing')]
    public string $goalType = 'ongoing';

    #[Validate('nullable|date')]
    public ?string $targetDate = null;

    public bool $saved = false;

    public function mount(?string $goalId = null): void
    {
        $this->goalId = $goalId;

        if ($goalId !== null) {
            $goal = Goal::find($goalId);

            if ($goal && $goal->user_id === auth()->id()) {
                $this->isEditMode = true;
                $this->title = $goal->title;
                $this->description = $goal->description;
                $this->goalType = $goal->goal_type->value;
                $this->targetDate = $goal->target_date?->toDateString();
            }
        }
    }

    public function save(): void
    {
        $this->validate();

        try {
            if ($this->goalId === null) {
                (new CreateGoal)->execute(auth()->user(), [
                    'title' => $this->title,
                    'description' => $this->description,
                    'goal_type' => $this->goalType,
                    'target_date' => $this->targetDate ?: null,
                ]);
                $this->reset(['title', 'description', 'targetDate']);
                $this->goalType = 'ongoing';
            } else {
                $goal = Goal::find($this->goalId);

                if ($goal && $goal->user_id === auth()->id()) {
                    (new UpdateGoal)->execute($goal, [
                        'title' => $this->title,
                        'description' => $this->description,
                        'target_date' => $this->targetDate ?: null,
                    ]);
                }
            }

            $this->saved = true;
            $this->dispatch('goal-saved');
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
        return view('livewire.goals.goal-form');
    }
}
