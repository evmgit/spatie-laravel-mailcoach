<?php

namespace Spatie\Mailcoach\Domain\Campaign\Exceptions;

use Exception;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;

class CouldNotUpdateCampaign extends Exception
{
    public static function beingSent(Campaign $campaign): self
    {
        return new static("The campaign `{$campaign->name}` cannot be updated because it is being sent.");
    }

    public static function alreadySent(Campaign $campaign): self
    {
        return new static("The campaign `{$campaign->name}` cannot be updated because it was already sent.");
    }
}
