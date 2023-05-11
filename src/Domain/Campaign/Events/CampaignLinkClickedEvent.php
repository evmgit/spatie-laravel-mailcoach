<?php

namespace Spatie\Mailcoach\Domain\Campaign\Events;

use Spatie\Mailcoach\Domain\Campaign\Models\CampaignClick;

class CampaignLinkClickedEvent
{
    public function __construct(
        public CampaignClick $campaignClick
    ) {
    }
}
