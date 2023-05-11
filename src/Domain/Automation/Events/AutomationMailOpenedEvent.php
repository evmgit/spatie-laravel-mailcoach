<?php

namespace Spatie\Mailcoach\Domain\Automation\Events;

use Spatie\Mailcoach\Domain\Automation\Models\AutomationMailOpen;

class AutomationMailOpenedEvent
{
    public function __construct(
        public AutomationMailOpen $automationMailOpen,
    ) {
    }
}
