<?php

namespace Spatie\Mailcoach\Http\Api\Controllers\EmailLists\Subscribers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Audience\Actions\Subscribers\ConfirmSubscriberAction;
use Spatie\Mailcoach\Domain\Audience\Enums\SubscriptionStatus;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Http\Api\Controllers\Concerns\RespondsToApiRequests;
use Spatie\Mailcoach\Http\Api\Requests\ConfirmSubscriberRequest;
use Symfony\Component\HttpFoundation\Response;

class ConfirmSubscriberController
{
    use AuthorizesRequests;
    use RespondsToApiRequests;

    public function __invoke(
        ConfirmSubscriberRequest $request,
        Subscriber $subscriber,
        ConfirmSubscriberAction $confirmSubscriberAction
    ) {
        $this->authorize('update', $subscriber->emailList);

        $this->ensureUnconfirmedSubscriber($subscriber);

        $confirmSubscriberAction->execute($subscriber);

        $this->respondOk();
    }

    protected function ensureUnconfirmedSubscriber(Subscriber $subscriber): void
    {
        if ($subscriber->status !== SubscriptionStatus::Unconfirmed) {
            abort(Response::HTTP_UNPROCESSABLE_ENTITY, 'The subscriber was already confirmed');
        }
    }
}
