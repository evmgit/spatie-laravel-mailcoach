<?php

namespace Spatie\Mailcoach\Domain\Campaign\Exceptions;

use Exception;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;

class CouldNotSegmentCampaign extends Exception
{
    public static function emailListNotSet(Campaign $campaign): self
    {
        return new static("Could not segment campaign `$campaign->name` because no list was be set. You must set a list before segmenting on subscribers.");
    }

    public static function tagDoesNotExistOnTheEmailList(string $tag, Campaign $campaign): self
    {
        return new static("Could not segment campaign `$campaign->name` because the specified tag `{$tag}` does not exist on list `{$campaign->emailList->name}`.");
    }

    public static function noTagsSet(Campaign $campaign): self
    {
        return new static("Could not segment campaign `$campaign->name` because no tags to segment on have been set.");
    }
}
