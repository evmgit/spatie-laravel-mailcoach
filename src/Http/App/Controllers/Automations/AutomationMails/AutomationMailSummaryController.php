<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Automations\AutomationMails;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Http\App\ViewModels\AutomationMailSummaryViewModel;

class AutomationMailSummaryController
{
    use AuthorizesRequests;

    public function __invoke(AutomationMail $automationMail)
    {
        $this->authorize('view', $automationMail);

        $viewModel = new AutomationMailSummaryViewModel($automationMail);

        return view('mailcoach::app.automations.mails.summary', $viewModel);
    }
}
