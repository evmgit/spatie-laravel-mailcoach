<?php

namespace Spatie\Mailcoach\Http\App\Controllers\EmailLists\Subscribers\UpdateStatus;

use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;

class ResubscribeController
{
    public function __invoke(Subscriber $subscriber)
    {
        if (! $subscriber->isUnsubscribed()) {
            flash()->error(__('Can only resubscribe unsubscribed subscribers'));

            return back();
        }

        $subscriber->update([
            'unsubscribed_at' => null,
        ]);

        flash()->success(__(':subscriber has been resubscribed.', ['subscriber' => $subscriber->email]));

        return back();
    }
}
