<?php

namespace Spatie\Mailcoach\Http\App\Controllers\EmailLists\Subscribers\UpdateStatus;

use Spatie\Mailcoach\Domain\Audience\Enums\SubscriptionStatus;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Symfony\Component\HttpFoundation\Response;

class ConfirmController
{
    public function __invoke(Subscriber $subscriber)
    {
        $this->ensureUnconfirmedSubscriber($subscriber);

        $subscriber->update([
            'subscribed_at' => now(),
            'unsubscribed_at' => null,
        ]);

        flash()->success(__(':subscriber has been confirmed.', ['subscriber' => $subscriber->email]));

        return back();
    }

    protected function ensureUnconfirmedSubscriber(Subscriber $subscriber): void
    {
        if ($subscriber->status !== SubscriptionStatus::UNCONFIRMED) {
            abort(Response::HTTP_UNPROCESSABLE_ENTITY, __('Can only subscribe unconfirmed emails'));
        }
    }
}
