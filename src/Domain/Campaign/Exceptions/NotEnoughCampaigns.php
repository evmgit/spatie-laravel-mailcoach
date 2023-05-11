<?php

namespace Spatie\Mailcoach\Domain\Campaign\Exceptions;

use Exception;

class NotEnoughCampaigns extends Exception
{
    public static function forOpens(int $opens): self
    {
        return new static("Not enough campaigns have been sent for `$opens` opens to be counted.");
    }

    public static function forClicks(int $clicks): self
    {
        return new static("Not enough campaigns have been sent for `$clicks` clicks to be counted.");
    }
}
