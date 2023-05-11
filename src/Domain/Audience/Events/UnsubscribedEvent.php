<?php

namespace Spatie\Mailcoach\Domain\Audience\Events;

use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Shared\Models\Send;

class UnsubscribedEvent
{
    public function __construct(
        public Subscriber $subscriber,
        public ?Send $send = null
    ) {
    }
}
