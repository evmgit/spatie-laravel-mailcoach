<?php

namespace Spatie\Mailcoach\Domain\Automation\Events;

use Spatie\Mailcoach\Domain\Shared\Models\Send;

class AutomationMailSentEvent
{
    public function __construct(
        public Send $send
    ) {
    }
}
