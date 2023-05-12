<?php

namespace Spatie\Mailcoach\Http\Api\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TemplateResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'html' => $this->html,
            'structured_html' => $this->structured_html,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
