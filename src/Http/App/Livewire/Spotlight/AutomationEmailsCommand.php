<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Spotlight;

use LivewireUI\Spotlight\Spotlight;
use LivewireUI\Spotlight\SpotlightCommand;

class AutomationEmailsCommand extends SpotlightCommand
{
    public function getName(): string
    {
        return __mc('Automation mails');
    }

    public function execute(Spotlight $spotlight)
    {
        $spotlight->redirect(route('mailcoach.automations.mails'));
    }
}
