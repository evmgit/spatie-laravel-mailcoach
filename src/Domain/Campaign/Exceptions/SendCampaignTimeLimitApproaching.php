<?php

namespace Spatie\Mailcoach\Domain\Campaign\Exceptions;

use Exception;

class SendCampaignTimeLimitApproaching extends Exception
{
    public static function make()
    {
        return new static();
    }
}
