<?php

namespace App\Livewire\Tasks;

use App\Domain\Tasks\Actions\CreateTask;
use App\Domain\Tasks\Actions\UpdateTask;
use App\Domain\Tasks\Models\Task;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Throwable;

class TaskForm extends Component
{
    // -------------------------------------------------------------------------
    // Properties
    // -------------------------------------------------------------------------

    /** When set, the component operates in edit mode. */
    public ?string $taskId = null;

    #[Validate('required|string|min:1|max:255')]
    public string $title = '';

    #[Validate('nullable|string|max:10000')]
    public ?string $description = null;

    #[Validate('nullable|in:low,medium,high')]
    public string $priority = 'medium';

    #[Validate('nullable|date')]
    public ?string $dueDate = null;

    /** Disabled for MVP — project assignment via EPIC-004. */
    public ?string $projectId = null;

    public bool $saved = false;

    // -------------------------------------------------------------------------
    // Lifecycle
    // -------------------------------------------------------------------------

    public function mount(?string $taskId = null): void
    {
        $this->taskId = $taskId;

        if ($taskId !== null) {
            $task = Task::find($taskId);

            if ($task && $task->user_id === auth()->id()) {
                $this->title = $task->title;
                $this->description = $task->description;
                $this->priority = $task->priority->value;
                $this->dueDate = $task->due_date?->toDateString();
                $this->projectId = $task->project_id;
            }
        }
    }

    // -------------------------------------------------------------------------
    // Actions
    // -------------------------------------------------------------------------

    public function save(): void
    {
        $this->validate();

        $data = [
            'title' => $this->title,
            'description' => $this->description,
            'priority' => $this->priority,
            'due_date' => $this->dueDate ?: null,
            'project_id' => $this->projectId,
        ];

        try {
            if ($this->taskId === null) {
                (new CreateTask)->execute(auth()->user(), $data);
                $this->reset(['title', 'description', 'priority', 'dueDate', 'projectId']);
                $this->priority = 'medium';
            } else {
                $task = Task::find($this->taskId);

                if ($task && $task->user_id === auth()->id()) {
                    (new UpdateTask)->execute($task, $data);
                }
            }

            $this->saved = true;
            $this->dispatch('task-saved');
        } catch (Throwable) {
            $this->addError('title', 'Gagal menyimpan. Silakan coba lagi.');
        }
    }

    public function resetSaved(): void
    {
        $this->saved = false;
    }

    // -------------------------------------------------------------------------
    // Render
    // -------------------------------------------------------------------------

    public function render()
    {
        return view('livewire.tasks.task-form');
    }
}
