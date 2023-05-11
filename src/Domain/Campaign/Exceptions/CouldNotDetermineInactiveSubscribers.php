<?php

namespace Spatie\Mailcoach\Domain\Campaign\Exceptions;

use Exception;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;

class CouldNotDetermineInactiveSubscribers extends Exception
{
    public static function create(EmailList $emailList): self
    {
        return new static("Could not determine inactive subscribers for `$emailList->name`. You need to pass at least one of didNotOpenPastNumberOfCampaigns & didNotClickPastNumberOfCampaigns");
    }
}
