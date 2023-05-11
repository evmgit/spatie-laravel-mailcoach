<?php

namespace Spatie\Mailcoach\Domain\Automation\Events;

use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;

class AutomationMailStatisticsCalculatedEvent
{
    public function __construct(
        public AutomationMail $automationMail
    ) {
    }
}
