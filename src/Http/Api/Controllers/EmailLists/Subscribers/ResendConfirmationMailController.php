<?php

namespace Spatie\Mailcoach\Http\Api\Controllers\EmailLists\Subscribers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Audience\Actions\Subscribers\SendConfirmSubscriberMailAction;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Http\Api\Controllers\Concerns\RespondsToApiRequests;
use Symfony\Component\HttpFoundation\Response;

class ResendConfirmationMailController
{
    use AuthorizesRequests;
    use RespondsToApiRequests;

    public function __invoke(
        Subscriber $subscriber,
        SendConfirmSubscriberMailAction $sendConfirmSubscriberMailAction
    ) {
        $this->authorize('update', $subscriber->emailList);

        $this->ensureUnconfirmedSubscribed($subscriber);

        $sendConfirmSubscriberMailAction->execute($subscriber);

        return $this->respondOk();
    }

    protected function ensureUnconfirmedSubscribed(Subscriber $subscriber): void
    {
        if (! $subscriber->isUnconfirmed()) {
            abort(Response::HTTP_UNPROCESSABLE_ENTITY, 'This email is not unconfirmed');
        }
    }
}
