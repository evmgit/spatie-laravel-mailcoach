<?php

namespace Spatie\Mailcoach\Http\Api\Controllers\EmailLists\Subscribers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Http\Api\Controllers\Concerns\RespondsToApiRequests;
use Symfony\Component\HttpFoundation\Response;

class UnsubscribeController
{
    use AuthorizesRequests;
    use RespondsToApiRequests;

    public function __invoke(Subscriber $subscriber)
    {
        $this->authorize('update', $subscriber->emailList);

        $this->ensureSubscribedSubscriber($subscriber);

        $subscriber->unsubscribe();

        return $this->respondOk();
    }

    protected function ensureSubscribedSubscriber(Subscriber $subscriber): void
    {
        if (! $subscriber->isSubscribed()) {
            abort(Response::HTTP_UNPROCESSABLE_ENTITY, 'This email was not subscribed');
        }
    }
}
