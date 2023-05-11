<?php

namespace Spatie\Mailcoach\Domain\Audience\Actions\Subscribers;

use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;

class DeleteSubscriberAction
{
    public function execute(Subscriber $subscriber): void
    {
        $subscriber->tags()->detach();

        $subscriber->delete();
    }
}
