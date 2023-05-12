<?php

namespace Spatie\Mailcoach\Http\App\Controllers\EmailLists\Subscribers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Http\App\Queries\EmailListSubscribersQuery;
use Spatie\SimpleExcel\SimpleExcelWriter;

class SubscribersExportController
{
    use AuthorizesRequests;

    public function __invoke(EmailList $emailList)
    {
        $this->authorize('view', $emailList);

        $subscribersQuery = new EmailListSubscribersQuery($emailList);

        $subscriberCsv = SimpleExcelWriter::streamDownload("{$emailList->name} subscribers.csv");

        $subscribersQuery->each(function (Subscriber $subscriber) use ($subscriberCsv) {
            $this->resetMaximumExecutionTime();
            $subscriberCsv->addRow($subscriber->toExportRow());
        });

        $subscriberCsv->toBrowser();
    }

    protected function resetMaximumExecutionTime(): void
    {
        $maximumExecutionTime = (int)ini_get('max_execution_time');

        set_time_limit($maximumExecutionTime);
    }
}
