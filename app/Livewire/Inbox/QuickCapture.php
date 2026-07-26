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

    public bool $saved = false;

    public function save(): void
    {
        $this->validate();

        try {
            $action = new CreateInboxItem;
            $action->execute(auth()->user(), $this->content);

            $this->content = '';
            $this->saved = true;
        } catch (Throwable) {
            $this->addError('content', 'Gagal menyimpan. Silakan coba lagi.');
        }
    }

    public function resetSaved(): void
    {
        $this->saved = false;
    }

    public function render()
    {
        return view('livewire.inbox.quick-capture');
    }
}
