<?php

namespace Spatie\Mailcoach\Http\App\Livewire;

trait LivewireFlash
{
    public function flashSuccess(string $message): self
    {
        $this->flash($message, 'success');

        return $this;
    }

    public function flashWarning(string $message): self
    {
        $this->flash($message, 'warning');

        return $this;
    }

    public function flashError(string $message): self
    {
        $this->flash($message, 'error');

        return $this;
    }

    public function flash(string $message, string $level = 'success')
    {
        $this->emit('notify', [$message, $level]);

        $this->dispatchBrowserEvent('notify', [
            'content' => $message,
            'type' => $level,
        ]);
    }
}
