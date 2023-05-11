<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Spotlight;

use LivewireUI\Spotlight\Spotlight;
use LivewireUI\Spotlight\SpotlightCommand;

class HomeCommand extends SpotlightCommand
{
    public function getName(): string
    {
        return __mc('Home');
    }

    public function execute(Spotlight $spotlight)
    {
        $spotlight->redirect(route(config('mailcoach.redirect_home', 'mailcoach.campaigns')));
    }
}
