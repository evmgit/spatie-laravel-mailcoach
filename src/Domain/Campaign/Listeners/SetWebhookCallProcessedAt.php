<?php

namespace Spatie\Mailcoach\Domain\Campaign\Listeners;

use Spatie\Mailcoach\Domain\Campaign\Events\WebhookCallProcessedEvent;

class SetWebhookCallProcessedAt
{
    public function handle(WebhookCallProcessedEvent $event)
    {
        $event->webhookCall->update([
            'processed_at' => now(),
        ]);
    }
}
