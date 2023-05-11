<?php

namespace Spatie\Mailcoach\Domain\Campaign\Events;

use Spatie\Mailcoach\Domain\Campaign\Models\CampaignOpen;

class CampaignOpenedEvent
{
    public function __construct(
        public CampaignOpen $campaignOpen
    ) {
    }
}
