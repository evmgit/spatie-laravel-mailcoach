<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Automations\AutomationMails;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\App\Queries\AutomatedMailQuery;

class AutomationMailsIndexController
{
    use AuthorizesRequests;
    use UsesMailcoachModels;

    public function __invoke(AutomatedMailQuery $automatedMailQuery)
    {
        $this->authorize("viewAny", static::getAutomationMailClass());

        return view('mailcoach::app.automations.mails.index', [
            'mails' => $automatedMailQuery->paginate(),
            'mailsQuery' => $automatedMailQuery,
            'totalMailsCount' => static::getAutomationMailClass()::count(),
        ]);
    }
}
