<?php

namespace Spatie\Mailcoach\Http\Api\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMail */
class TransactionalMailResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'subject' => $this->subject,
            'from' => $this->from,
            'to' => $this->to,
            'cc' => $this->cc,
            'bcc' => $this->bcc,
            'body' => $this->body,
            'track_clicks' => $this->track_clicks,
            'track_opens' => $this->track_opens,
            'created_at' => $this->created_at,
        ];
    }
}
