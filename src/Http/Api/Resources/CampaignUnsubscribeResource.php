<?php

namespace Spatie\Mailcoach\Http\Api\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CampaignUnsubscribeResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'campaign_id' => (int)$this->campaign_id,
            'campaign' => new CampaignResource($this->whenLoaded('campaign')),

            'subscriber_id' => (int)$this->subscriber_id,
            'subscriber_email' => $this->subscriber->email,
            'subscriber' => new SubscriberResource($this->whenLoaded('subscriber')),
        ];
    }
}
