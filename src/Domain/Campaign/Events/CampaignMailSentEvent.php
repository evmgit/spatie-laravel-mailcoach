<?php

namespace Spatie\Mailcoach\Domain\Campaign\Events;

use Spatie\Mailcoach\Domain\Shared\Models\Send;

class CampaignMailSentEvent
{
    public function __construct(
        public Send $send
    ) {
    }
}
