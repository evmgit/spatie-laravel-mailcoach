<?php

namespace Spatie\Mailcoach\Http\Api\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \Spatie\Mailcoach\Domain\Shared\Models\Send */
class SendResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'uuid' => $this->uuid,
            'transport_message_id' => $this->transport_message_id,
            'campaign_uuid' => $this->campaign?->uuid,
            'automation_mail_uuid' => $this->automationMail?->uuid,
            'transactional_mail_log_item_uuid' => $this->transactionalMailLogItem?->uuid,
            'subscriber_uuid' => $this->subscriber?->uuid,
            'sent_at' => $this->sent_at,
            'failed_at' => $this->failed_at,
            'failure_reason' => $this->failure_reason,
            'open_count' => $this->open_count,
            'click_count' => $this->click_count,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
