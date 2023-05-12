<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Automations\AutomationMails;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Http\App\Queries\AutomationMailLinksQuery;

class AutomationMailClicksController
{
    use AuthorizesRequests;

    public function __invoke(AutomationMail $mail)
    {
        $this->authorize('view', $mail);

        $campaignLinksQuery = new AutomationMailLinksQuery($mail);

        return view('mailcoach::app.automations.mails.clicks', [
            'mail' => $mail,
            'links' => $campaignLinksQuery->paginate(),
            'totalLinksCount' => $mail->links()->count(),
        ]);
    }
}
