<?php

namespace Spatie\Mailcoach\Domain\Automation\Exceptions;

use Exception;
use Spatie\Mailcoach\Domain\Automation\Models\Automation ;

class CouldNotStartAutomation extends Exception
{
    public static function started(Automation $automation): self
    {
        return new static("The automation `{$automation->name}` with id `{$automation->id}` can't be started, because it is already started.");
    }

    public static function noInterval(Automation $automation): self
    {
        return new static("The automation `{$automation->name}` with id `{$automation->id}` can't be started, because it has no interval.");
    }

    public static function noActions(Automation $automation): self
    {
        return new static("The automation `{$automation->name}` with id `{$automation->id}` can't be started, because it has no actions to run.");
    }

    public static function noTrigger(Automation $automation): self
    {
        return new static("The automation `{$automation->name}` with id `{$automation->id}` can't be started, because it has no trigger.");
    }

    public static function noListSet(Automation $automation): self
    {
        return new static("The automation `{$automation->name}` with id `{$automation->id}` can't be started, because there is no list set to send it to.");
    }
}
