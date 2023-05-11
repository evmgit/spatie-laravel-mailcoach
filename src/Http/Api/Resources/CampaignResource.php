<?php

namespace Spatie\Mailcoach\Http\Api\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \Spatie\Mailcoach\Domain\Campaign\Models\Campaign */
class CampaignResource extends JsonResource
{
    public function toArray($request)
    {
        $fields = collect($this->getTemplateFieldValues())->map(function ($field) {
            return $field['markdown'] ?? $field; // If we have markdown content, only return the markdown
        })->toArray();

        return [
            'uuid' => $this->uuid,
            'name' => $this->name,

            'email_list_uuid' => $this->emailList->uuid,
            'email_list' => new EmailListResource($this->whenLoaded('emailList')),

            'template_uuid' => $this->template?->uuid,
            'template' => new TemplateResource($this->whenLoaded('template')),

            'from_email' => $this->from_email,
            'from_name' => $this->subject,

            'status' => $this->status,

            'html' => $this->html,
            'structured_html' => $this->structured_html,
            'email_html' => $this->email_html,
            'webview_html' => $this->webview_html,

            'fields' => $fields,

            'mailable_class' => $this->mailable_class,

            'utm_tags' => $this->utm_tags,

            'sent_to_number_of_subscribers' => $this->sent_to_number_of_subscribers,

            'segment_class' => $this->segment_cass,
            'segment_description' => $this->segment_description,
            'open_count' => $this->open_count,

            'unique_open_count' => $this->unique_open_count,
            'open_rate' => $this->open_rate,
            'click_count' => $this->click_count,
            'unique_click_count' => $this->unique_click_count,
            'click_rate' => $this->click_rate,
            'unsubscribe_count' => $this->unsubscribe_count,
            'unsubscribe_rate' => $this->unsubscribe_rate,
            'bounce_count' => $this->bounce_count,
            'bounce_rate' => $this->bounce_rate,

            'sent_at' => $this->sent_at,
            'statistics_calculated_at' => $this->statistics_calculated_at,
            'scheduled_at' => $this->scheduled_at,

            'last_modified_at' => $this->last_modified_at,

            'summary_mail_sent_at' => $this->summary_mail_sent_at,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
