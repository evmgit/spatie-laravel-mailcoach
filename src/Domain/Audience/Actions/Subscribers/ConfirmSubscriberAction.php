<?php

namespace Spatie\Mailcoach\Domain\Audience\Actions\Subscribers;

use Spatie\Mailcoach\Domain\Audience\Actions\Subscribers\Concerns\SendsWelcomeMail;
use Spatie\Mailcoach\Domain\Audience\Events\SubscribedEvent;
use Spatie\Mailcoach\Domain\Audience\Events\TagAddedEvent;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;

class ConfirmSubscriberAction
{
    use SendsWelcomeMail;

    protected bool $sendWelcomeMail = true;

    public function doNotSendWelcomeMail(): self
    {
        $this->sendWelcomeMail = false;

        return $this;
    }

    public function execute(Subscriber $subscriber): void
    {
        $subscriber->update([
            'subscribed_at' => now(),
        ]);

        if ($this->sendWelcomeMail) {
            $this->sendWelcomeMail($subscriber);
        }

        foreach ($subscriber->tags as $tag) {
            event(new TagAddedEvent($subscriber, $tag));
        }

        event(new SubscribedEvent($subscriber));
    }
}
