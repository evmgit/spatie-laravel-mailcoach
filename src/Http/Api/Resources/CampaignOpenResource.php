<?php

namespace Spatie\Mailcoach\Http\Api\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CampaignOpenResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'uuid' => $this->uuid,
            'subscriber_uuid' => $this->subscriber_uuid,
            'subscriber_email_list_uuid' => (int) $this->subscriber_email_list_uuid,
            'subscriber_email' => $this->subscriber_email,
            'open_count' => (int) $this->open_count,
            'first_opened_at' => $this->first_opened_at,
        ];
    }
}
