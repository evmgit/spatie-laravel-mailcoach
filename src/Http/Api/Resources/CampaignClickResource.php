<?php

namespace Spatie\Mailcoach\Http\Api\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CampaignClickResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'url' => $this->url,
            'unique_click_count' => $this->unique_click_count,
            'click_count' => $this->click_count,
        ];
    }
}
