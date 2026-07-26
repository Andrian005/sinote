<?php

namespace App\Livewire\Inbox;

use App\Domain\Inbox\Actions\DiscardInboxItem;
use App\Domain\Inbox\Actions\TriageInboxItem;
use App\Domain\Inbox\Exceptions\InboxItemAlreadyProcessedException;
use App\Domain\Inbox\Models\InboxItem;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

class InboxList extends Component
{
    use WithPagination;

    /** Flash message text, null when no flash is active. */
    public ?string $flash = null;

    /** Whether the flash represents an error (red) vs success (green). */
    public bool $flashIsError = false;

    // -------------------------------------------------------------------------
    // Data
    // -------------------------------------------------------------------------

    /**
     * Paginated list of unprocessed InboxItems for the authenticated user,
     * ordered newest-first (FSD 1.2 — Triage list order).
     */
    public function getInboxItemsProperty(): LengthAwarePaginator
    {
        return InboxItem::where('user_id', auth()->id())
            ->unprocessed()
            ->orderByDesc('created_at')
            ->paginate(10);
    }

    // -------------------------------------------------------------------------
    // Actions
    // -------------------------------------------------------------------------

    /**
     * Triage an InboxItem to 'task' or 'note'.
     *
     * For MVP: TriageInboxItem contracts are not yet bound to real
     * implementations (EPIC-003/005), so triage calls will throw unless
     * a binding is registered. The try/catch surfaces a friendly message.
     *
     * @param  string  $inboxItemId  ULID of the item to triage.
     * @param  string  $targetType  'task' or 'note'.
     */
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
            /** @var TriageInboxItem $action */
            $action = app(TriageInboxItem::class);
            $action->execute(auth()->user(), $item, $targetType);

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

    /**
     * Discard an InboxItem.
     *
     * @param  string  $inboxItemId  ULID of the item to discard.
     */
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
            $action = new DiscardInboxItem;
            $action->execute($item);

            $this->setFlash('Item berhasil dihapus dari Inbox.');
            $this->resetPage();
        } catch (InboxItemAlreadyProcessedException) {
            $this->setFlash('Item ini sudah diproses sebelumnya.', error: true);
        }
    }

    // -------------------------------------------------------------------------
    // Flash helpers
    // -------------------------------------------------------------------------

    /** Set a flash message. Called by Alpine's 3-second timer to clear. */
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
        return view('livewire.inbox.inbox-list', [
            'inboxItems' => $this->inboxItems,
        ]);
    }
}
