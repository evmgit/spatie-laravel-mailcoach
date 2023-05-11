<?php

namespace Spatie\Mailcoach\Domain\Campaign\Listeners;

use Spatie\Mailcoach\Domain\Campaign\Enums\TagType;
use Spatie\Mailcoach\Domain\Campaign\Events\CampaignOpenedEvent;

class AddCampaignOpenedTag
{
    public function handle(CampaignOpenedEvent $event)
    {
        $campaign = $event->campaignOpen->campaign;

        if (! $campaign->add_subscriber_tags) {
            return;
        }

        $subscriber = $event->campaignOpen->subscriber;
        $subscriber->addTag("campaign-{$campaign->uuid}-opened", TagType::Mailcoach);
    }
}
