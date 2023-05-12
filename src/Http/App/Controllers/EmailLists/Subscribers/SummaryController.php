<?php

namespace Spatie\Mailcoach\Http\App\Controllers\EmailLists\Subscribers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Http\App\ViewModels\EmailListSummaryViewModel;

class SummaryController
{
    use AuthorizesRequests;

    public function __invoke(EmailList $emailList)
    {
        $this->authorize('view', $emailList);

        $viewModel = new EmailListSummaryViewModel($emailList);

        return view('mailcoach::app.emailLists.summary', $viewModel);
    }
}
