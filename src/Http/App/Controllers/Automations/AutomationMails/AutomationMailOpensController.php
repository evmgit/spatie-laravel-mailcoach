<?php


namespace Spatie\Mailcoach\Http\App\Controllers\Automations\AutomationMails;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Http\App\Queries\AutomationMailOpensQuery;

class AutomationMailOpensController
{
    use AuthorizesRequests;

    public function __invoke(AutomationMail $mail)
    {
        $this->authorize('view', $mail);

        $automationMailOpens = new AutomationMailOpensQuery($mail);

        return view('mailcoach::app.automations.mails.opens', [
            'mail' => $mail,
            'mailOpens' => $automationMailOpens->paginate(),
            'totalMailOpensCount' => $automationMailOpens->totalCount,
        ]);
    }
}
