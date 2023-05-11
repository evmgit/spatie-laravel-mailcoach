<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Spotlight;

use LivewireUI\Spotlight\Spotlight;
use LivewireUI\Spotlight\SpotlightCommand;

class TransactionalTemplatesCommand extends SpotlightCommand
{
    public function getName(): string
    {
        return __mc('Transactional templates');
    }

    public function execute(Spotlight $spotlight)
    {
        $spotlight->redirect(route('mailcoach.transactionalMails.templates'));
    }
}
