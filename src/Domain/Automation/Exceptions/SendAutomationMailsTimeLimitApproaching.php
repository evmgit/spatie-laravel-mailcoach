<?php

namespace Spatie\Mailcoach\Domain\Automation\Exceptions;

use Exception;

class SendAutomationMailsTimeLimitApproaching extends Exception
{
    public static function make()
    {
        return new static();
    }
}
