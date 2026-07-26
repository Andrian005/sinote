<?php

namespace App\Livewire\Inbox;

use App\Domain\Inbox\Actions\DiscardInboxItem;
use App\Domain\Inbox\Actions\TriageInboxItem;
use App\Domain\Inbox\Exceptions\InboxItemAlreadyProcessedException;
use App\Domain\Inbox\Models\InboxItem;
use App\Livewire\Concerns\WithFlashMessage;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

class InboxList extends Component
{
    use WithFlashMessage, WithPagination;

    public function getInboxItemsProperty(): LengthAwarePaginator
    {
        return InboxItem::where('user_id', auth()->id())
            ->unprocessed()
            ->orderByDesc('created_at')
            ->paginate(10);
    }

    public function triage(string $inboxItemId, string $targetType): void
    {
        $item = InboxItem::find($inboxItemId);

        if (! $item) {
            return;
        }

        if (Gate::denies('triage', $item)) {
            $this->setFlash('Anda tidak memiliki akses untuk melakukan aksi ini.', error: true);

            return;
        }

        try {
            app(TriageInboxItem::class)->execute(auth()->user(), $item, $targetType);

            $label = match ($targetType) {
                'task' => 'Task',
                'note' => 'Note',
                default => ucfirst($targetType),
            };

            $this->setFlash("Item berhasil dikonversi menjadi {$label}.");
            $this->resetPage();
        } catch (InboxItemAlreadyProcessedException) {
            $this->setFlash('Item ini sudah diproses sebelumnya.', error: true);
        } catch (\Throwable) {
            $this->setFlash('Gagal mengkonversi item. Silakan coba lagi.', error: true);
        }
    }

    public function discard(string $inboxItemId): void
    {
        $item = InboxItem::find($inboxItemId);

        if (! $item) {
            return;
        }

        if (Gate::denies('delete', $item)) {
            $this->setFlash('Anda tidak memiliki akses untuk melakukan aksi ini.', error: true);

            return;
        }

        try {
            (new DiscardInboxItem)->execute($item);
            $this->setFlash('Item berhasil dihapus dari Inbox.');
            $this->resetPage();
        } catch (InboxItemAlreadyProcessedException) {
            $this->setFlash('Item ini sudah diproses sebelumnya.', error: true);
        }
    }

    public function render()
    {
        return view('livewire.inbox.inbox-list', [
            'inboxItems' => $this->inboxItems,
        ]);
    }
}
