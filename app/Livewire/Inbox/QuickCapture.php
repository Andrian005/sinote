<?php

namespace App\Livewire\Inbox;

use App\Domain\Inbox\Actions\CreateInboxItem;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Throwable;

class QuickCapture extends Component
{
    #[Validate('required|string|min:1|max:5000')]
    public string $content = '';

    /** Controls success flash message visibility. */
    public bool $saved = false;

    /**
     * Save a new InboxItem via the CreateInboxItem Action.
     *
     * On success: reset content, show flash for 3 s (JS handles the fade).
     * On failure: preserve content so the user does not lose their text (FSD 1.1).
     */
    public function save(): void
    {
        $this->validate();

        try {
            $action = new CreateInboxItem;
            $action->execute(auth()->user(), $this->content);

            $this->content = '';
            $this->saved = true;
        } catch (Throwable) {
            // Content is intentionally preserved on failure (FSD 1.1 Exception Handling)
            $this->addError('content', 'Gagal menyimpan. Silakan coba lagi.');
        }
    }

    /** Called by Alpine after the 3-second fade to hide the flash message. */
    public function resetSaved(): void
    {
        $this->saved = false;
    }

    public function render()
    {
        return view('livewire.inbox.quick-capture');
    }
}
