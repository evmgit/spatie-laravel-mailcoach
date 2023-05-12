<?php

namespace Spatie\Mailcoach\Http\App\Controllers;

class HomeController
{
    public function __invoke()
    {
        return redirect()->route('mailcoach.campaigns');
    }
}
