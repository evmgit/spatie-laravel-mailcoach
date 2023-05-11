<?php

namespace Spatie\Mailcoach\Domain\Campaign\Events;

use Spatie\Mailcoach\Domain\Shared\Models\Send;

class BounceRegisteredEvent
{
    public function __construct(
        public Send $send
    ) {
    }
}
