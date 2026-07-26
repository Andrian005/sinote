<?php

namespace App\Livewire\Concerns;

trait WithFlashMessage
{
    public ?string $flash = null;

    public bool $flashIsError = false;

    public function clearFlash(): void
    {
        $this->flash = null;
        $this->flashIsError = false;
    }

    protected function setFlash(string $message, bool $error = false): void
    {
        $this->flash = $message;
        $this->flashIsError = $error;
    }
}
