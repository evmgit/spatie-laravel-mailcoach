<?php

namespace Spatie\Mailcoach\Domain\Automation\Events;

use Spatie\Mailcoach\Domain\Automation\Models\AutomationMailClick;

class AutomationMailLinkClickedEvent
{
    public function __construct(
        public AutomationMailClick $automationMailClick,
    ) {
    }
}
