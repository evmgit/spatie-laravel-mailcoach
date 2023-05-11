<?php

namespace Spatie\Mailcoach\Http\App\Controllers;

class HomeController
{
    public function __invoke()
    {
        return redirect()->route(config('mailcoach.redirect_home', 'mailcoach.dashboard'));
    }
}
