<?php

namespace App\Livewire\Tasks;

use App\Domain\Tasks\Actions\ArchiveTask;
use App\Domain\Tasks\Actions\UpdateTaskStatus;
use App\Domain\Tasks\Enums\TaskStatus;
use App\Domain\Tasks\Exceptions\InvalidTaskTransitionException;
use App\Domain\Tasks\Models\Task;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class TaskList extends Component
{
    use WithPagination;

    /**
     * Active filter.
     * Accepted: 'active' (todo+in_progress), 'done', 'archived', 'all'
     */
    public string $filter = 'active';

    /**
     * When > 0, pagination is hidden and results capped at this number.
     * Used by the Dashboard widget (limit=5, showPagination=false).
     */
    public int $limit = 0;

    public ?string $flash = null;

    public bool $flashIsError = false;

    // -------------------------------------------------------------------------
    // Lifecycle
    // -------------------------------------------------------------------------

    public function mount(string $filter = 'active', int $limit = 0): void
    {
        $this->filter = $filter;
        $this->limit = $limit;
    }

    // -------------------------------------------------------------------------
    // Data
    // -------------------------------------------------------------------------

    /**
     * Paginated (or limited) tasks for the authenticated user.
     * Ordered by priority weight DESC, due_date ASC (nulls last).
     */
    public function getTasksProperty(): LengthAwarePaginator|Collection
    {
        $query = Task::where('user_id', auth()->id())
            ->orderByRaw("CASE priority WHEN 'high' THEN 3 WHEN 'medium' THEN 2 ELSE 1 END DESC")
            ->orderByRaw('due_date ASC NULLS LAST');

        match ($this->filter) {
            'active' => $query->active(),
            'done' => $query->done(),
            'archived' => $query->archived(),
            default => null, // 'all' — no filter
        };

        if ($this->limit > 0) {
            return $query->limit($this->limit)->get();
        }

        return $query->paginate(15);
    }

    // -------------------------------------------------------------------------
    // Actions
    // -------------------------------------------------------------------------

    public function updateStatus(string $taskId, string $newStatus): void
    {
        $task = Task::find($taskId);

        if (! $task) {
            return;
        }

        if (Gate::denies('updateStatus', $task)) {
            $this->setFlash('Anda tidak memiliki akses untuk melakukan aksi ini.', error: true);

            return;
        }

        try {
            $status = TaskStatus::from($newStatus);
            (new UpdateTaskStatus)->execute($task, $status);
            $this->setFlash('Status task berhasil diperbarui.');
            $this->resetPage();
        } catch (InvalidTaskTransitionException $e) {
            $this->setFlash('Transisi status tidak valid.', error: true);
        } catch (\Throwable) {
            $this->setFlash('Gagal memperbarui status. Silakan coba lagi.', error: true);
        }
    }

    public function archive(string $taskId): void
    {
        $task = Task::find($taskId);

        if (! $task) {
            return;
        }

        if (Gate::denies('archive', $task)) {
            $this->setFlash('Anda tidak memiliki akses untuk melakukan aksi ini.', error: true);

            return;
        }

        try {
            (new ArchiveTask(new UpdateTaskStatus))->execute($task);
            $this->setFlash('Task berhasil diarsipkan.');
            $this->resetPage();
        } catch (InvalidTaskTransitionException) {
            $this->setFlash('Task sudah diarsipkan sebelumnya.', error: true);
        }
    }

    // -------------------------------------------------------------------------
    // Event listener
    // -------------------------------------------------------------------------

    /** Refresh task list when a task is saved via TaskForm. */
    #[On('task-saved')]
    public function refreshList(): void
    {
        $this->resetPage();
        unset($this->tasks);
    }

    // -------------------------------------------------------------------------
    // Flash helpers
    // -------------------------------------------------------------------------

    public function clearFlash(): void
    {
        $this->flash = null;
        $this->flashIsError = false;
    }

    private function setFlash(string $message, bool $error = false): void
    {
        $this->flash = $message;
        $this->flashIsError = $error;
    }

    // -------------------------------------------------------------------------
    // Render
    // -------------------------------------------------------------------------

    public function render()
    {
        return view('livewire.tasks.task-list', [
            'tasks' => $this->tasks,
        ]);
    }
}
