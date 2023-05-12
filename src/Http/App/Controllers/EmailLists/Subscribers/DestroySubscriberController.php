<?php

namespace Spatie\Mailcoach\Http\App\Controllers\EmailLists\Subscribers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Audience\Actions\Subscribers\DeleteSubscriberAction;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Shared\Support\Config;

class DestroySubscriberController
{
    use AuthorizesRequests;

    public function __invoke(EmailList $emailList, Subscriber $subscriber)
    {
        $this->authorize('update', $emailList);

        /** @var DeleteSubscriberAction $deleteSubscriberAction */
        $deleteSubscriberAction = Config::getAudienceActionClass('delete_subscriber', DeleteSubscriberAction::class);

        $deleteSubscriberAction->execute($subscriber);

        flash()->success(__('Subscriber :subscriber was deleted.', ['subscriber' => $subscriber->email]));

        return back();
    }
}
