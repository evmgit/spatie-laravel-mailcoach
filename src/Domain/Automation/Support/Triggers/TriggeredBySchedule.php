<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Triggers;

interface TriggeredBySchedule
{
    public function trigger(): void;
}
