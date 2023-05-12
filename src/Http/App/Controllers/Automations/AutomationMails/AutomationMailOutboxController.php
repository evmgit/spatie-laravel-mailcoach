<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Automations\AutomationMails;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Http\App\Queries\AutomationMailSendsQuery;

class AutomationMailOutboxController
{
    use AuthorizesRequests;

    public function __invoke(AutomationMail $automationMail)
    {
        $this->authorize('view', $automationMail);

        $sendsQuery = new AutomationMailSendsQuery($automationMail);

        return view('mailcoach::app.automations.mails.outbox', [
            'mail' => $automationMail,
            'sends' => $sendsQuery->paginate(),
            'totalSends' => $automationMail->sends()->count(),
            'totalPending' => $automationMail->sends()->pending()->count(),
            'totalSent' => $automationMail->sends()->sent()->count(),
            'totalFailed' => $automationMail->sends()->failed()->count(),
            'totalBounces' => $automationMail->sends()->bounced()->count(),
            'totalComplaints' => $automationMail->sends()->complained()->count(),
        ]);
    }
}
