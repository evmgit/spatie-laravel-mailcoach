<?php

namespace Spatie\Mailcoach\Domain\Campaign\Events;

use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;

class CampaignSentEvent
{
    public function __construct(
        public Campaign $campaign
    ) {
    }
}
