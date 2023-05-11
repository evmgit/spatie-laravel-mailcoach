<?php

namespace Spatie\Mailcoach\Domain\Audience\Events;

use Spatie\Mailcoach\Domain\Shared\Models\Send;

class ComplaintRegisteredEvent
{
    public function __construct(
        public Send $send
    ) {
    }
}
