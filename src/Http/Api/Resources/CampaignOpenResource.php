<?php

namespace Spatie\Mailcoach\Http\Api\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CampaignOpenResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'subscriber_id' => (int)$this->subscriber_id,
            'subscriber_email_list_id' => (int)$this->subscriber_email_list_id,
            'subscriber_email' => $this->subscriber->email,
            'open_count' => (int)$this->open_count,
            'first_opened_at' => $this->first_opened_at,
        ];
    }
}
