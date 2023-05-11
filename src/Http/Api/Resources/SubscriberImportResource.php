<?php

namespace Spatie\Mailcoach\Http\Api\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SubscriberImportResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'uuid' => $this->uuid,
            'subscribers_csv' => $this->subscribers_csv,
            'status' => $this->status,
            'email_list_uuid' => $this->emailList->uuid,
            'subscribe_unsubscribed' => (bool) $this->subscribe_unsubscribed,
            'unsubscribe_others' => (bool) $this->unsubscribe_others,
            'replace_tags' => (bool) $this->replace_tags,
            'imported_subscribers_count' => (int) $this->imported_subscribers_count,
            'error_count' => (int) $this->error_count,
        ];
    }
}
