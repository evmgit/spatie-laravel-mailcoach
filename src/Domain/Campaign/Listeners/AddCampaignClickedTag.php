<?php

namespace Spatie\Mailcoach\Domain\Campaign\Listeners;

use Spatie\Mailcoach\Domain\Campaign\Enums\TagType;
use Spatie\Mailcoach\Domain\Campaign\Events\CampaignLinkClickedEvent;
use Spatie\Mailcoach\Domain\Shared\Support\LinkHasher;

class AddCampaignClickedTag
{
    public function handle(CampaignLinkClickedEvent $event)
    {
        $campaign = $event->campaignClick->link->campaign;
        $subscriber = $event->campaignClick->subscriber;

        if ($campaign->add_subscriber_tags) {
            $subscriber->addTag("campaign-{$campaign->uuid}-clicked", TagType::Mailcoach);
        }

        if ($campaign->add_subscriber_link_tags) {
            $hash = LinkHasher::hash(
                sendable: $event->campaignClick->send->campaign,
                url: $event->campaignClick->link->url,
            );

            $subscriber->addTag($hash, TagType::Mailcoach);
        }
    }
}
