<?php

namespace Spatie\Mailcoach\Domain\Campaign\Events;

use Spatie\WebhookClient\Models\WebhookCall;

class WebhookCallProcessedEvent
{
    public function __construct(
        public WebhookCall $webhookCall
    ) {
    }
}
