<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Automations\AutomationMails;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\MainNavigation;

class AutomationMailContentController
{
    use AuthorizesRequests;

    public function edit(AutomationMail $automationMail)
    {
        $this->authorize('update', $automationMail);

        app(MainNavigation::class)->activeSection()?->add($automationMail->name, route('mailcoach.automations.mails'));

        return view('mailcoach::app.automations.mails.content', [
            'mail' => $automationMail,
        ]);
    }
}
