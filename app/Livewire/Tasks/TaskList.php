<?php

namespace App\Livewire\Tasks;

use App\Domain\Tasks\Actions\ArchiveTask;
use App\Domain\Tasks\Actions\UpdateTaskStatus;
use App\Domain\Tasks\Enums\TaskStatus;
use App\Domain\Tasks\Exceptions\InvalidTaskTransitionException;
use App\Domain\Tasks\Models\Task;
use App\Livewire\Concerns\WithFlashMessage;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class TaskList extends Component
{
    use WithFlashMessage, WithPagination;

    public string $filter = 'active';

    public int $limit = 0;

    public function mount(string $filter = 'active', int $limit = 0): void
    {
        $this->filter = $filter;
        $this->limit = $limit;
    }

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

    #[On('task-saved')]
    public function refreshList(): void
    {
        $this->resetPage();
        unset($this->tasks);
    }

    public function render()
    {
        return view('livewire.tasks.task-list', [
            'tasks' => $this->tasks,
        ]);
    }
}
