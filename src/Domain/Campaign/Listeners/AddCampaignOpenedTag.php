<?php

namespace Spatie\Mailcoach\Domain\Campaign\Listeners;

use Spatie\Mailcoach\Domain\Campaign\Enums\TagType;
use Spatie\Mailcoach\Domain\Campaign\Events\CampaignOpenedEvent;

class AddCampaignOpenedTag
{
    public function handle(CampaignOpenedEvent $event)
    {
        $campaign = $event->campaignOpen->campaign;
        $subscriber = $event->campaignOpen->subscriber;

        $subscriber->addTag("campaign-{$campaign->id}-opened", TagType::MAILCOACH);
    }
}
