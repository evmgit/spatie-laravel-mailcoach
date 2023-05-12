<?php

namespace Spatie\Mailcoach\Http\App\Controllers\EmailLists\Subscribers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\App\Queries\CampaignSendQuery;

class ReceivedCampaignsController
{
    use AuthorizesRequests;
    use UsesMailcoachModels;

    public function __invoke(EmailList $emailList, Subscriber $subscriber)
    {
        $this->authorize('view', $emailList);

        $sendQuery = new CampaignSendQuery($subscriber);

        return view('mailcoach::app.emailLists.subscribers.receivedCampaigns', [
            'subscriber' => $subscriber,
            'sends' => $sendQuery->paginate(),
            'totalSendsCount' => $this->getSendClass()::query()->where('subscriber_id', $subscriber->id)->count(),
        ]);
    }
}
