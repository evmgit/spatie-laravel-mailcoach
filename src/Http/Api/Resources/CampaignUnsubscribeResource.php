<?php

namespace Spatie\Mailcoach\Http\Api\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CampaignUnsubscribeResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'campaign_uuid' => $this->campaign->uuid,
            'campaign' => new CampaignResource($this->whenLoaded('campaign')),

            'subscriber_uuid' => $this->subscriber->uuid,
            'subscriber_email' => $this->subscriber->email,
            'subscriber' => new SubscriberResource($this->whenLoaded('subscriber')),
        ];
    }
}
