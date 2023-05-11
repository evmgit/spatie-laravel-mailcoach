<?php

namespace Spatie\Mailcoach\Domain\Audience\Actions\Subscribers;

use Spatie\Mailcoach\Domain\Audience\Events\SubscribedEvent;
use Spatie\Mailcoach\Domain\Audience\Events\TagAddedEvent;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;

class ConfirmSubscriberAction
{
    public function execute(Subscriber $subscriber): void
    {
        $subscriber->update([
            'subscribed_at' => now(),
        ]);

        foreach ($subscriber->tags as $tag) {
            event(new TagAddedEvent($subscriber, $tag));
        }

        event(new SubscribedEvent($subscriber));
    }
}
