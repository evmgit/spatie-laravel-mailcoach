<?php

namespace Spatie\Mailcoach\Http\Api\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \Spatie\Mailcoach\Domain\Audience\Models\Subscriber */
class SubscriberResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'uuid' => $this->uuid,
            'email_list_uuid' => $this->emailList->uuid,

            'email' => $this->email,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'extra_attributes' => $this->extra_attributes,
            'tags' => $this->tags->pluck('name'),

            'subscribed_at' => $this->subscribed_at,
            'unsubscribed_at' => $this->unsubscribed_at,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
