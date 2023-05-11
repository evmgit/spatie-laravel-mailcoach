<?php

namespace Spatie\Mailcoach\Domain\Automation\Enums;

enum AutomationStatus: string
{
    case Paused = 'paused';
    case Started = 'started';
}
