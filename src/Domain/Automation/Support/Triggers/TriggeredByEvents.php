<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Triggers;

interface TriggeredByEvents
{
    public function subscribe($events): void;
}
