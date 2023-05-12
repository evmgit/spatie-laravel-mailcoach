<?php

namespace Spatie\Mailcoach\Http\App\Controllers\EmailLists\Subscribers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\App\Requests\EmailLists\Subscribers\CreateSubscriberRequest;

class CreateSubscriberController
{
    use AuthorizesRequests;
    use UsesMailcoachModels;

    public function store(
        EmailList $emailList,
        CreateSubscriberRequest $updateSubscriberRequest
    ) {
        $this->authorize('update', $emailList);

        $this->getSubscriberClass()::createWithEmail($updateSubscriberRequest->email)
            ->withAttributes($updateSubscriberRequest->subscriberAttributes())
            ->skipConfirmation()
            ->subscribeTo($emailList);

        flash()->success(__('Subscriber :subscriber was created.', ['subscriber' => $updateSubscriberRequest->email]));

        return back();
    }
}
