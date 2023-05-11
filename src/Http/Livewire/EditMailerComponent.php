<?php

namespace Spatie\Mailcoach\Http\Livewire;

use Livewire\Component;
use Spatie\Mailcoach\Domain\Settings\Models\Mailer;

class EditMailerComponent extends Component
{
    public Mailer $mailer;

    public function mount(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function render()
    {
        return view("mailcoach::app.configuration.mailers.wizards.{$this->mailer->transport->value}.index")
            ->layout('mailcoach::app.layouts.settings', ['title' => $this->mailer->name]);
    }
}
