<?php

namespace Spatie\Mailcoach\Domain\Campaign\Events;

use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;

class CampaignStatisticsCalculatedEvent
{
    public function __construct(
        public Campaign $campaign
    ) {
    }
}
