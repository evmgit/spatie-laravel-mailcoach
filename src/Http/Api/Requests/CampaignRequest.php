<?php

namespace Spatie\Mailcoach\Http\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Spatie\Mailcoach\Domain\Audience\Models\TagSegment;
use Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus;
use Spatie\Mailcoach\Domain\Campaign\Models\Template;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class CampaignRequest extends FormRequest
{
    use UsesMailcoachModels;

    public function rules()
    {
        return [
            'name' => ['required'],
            'type' => ['nullable', Rule::in([CampaignStatus::DRAFT])],
            'email_list_id' => ['required', Rule::exists($this->getEmailListTableName(), 'id')],
            'segment_id' => [Rule::exists((new TagSegment())->getTable())],
            'html' => '',
            'mailable_class' => '',
            'track_opens' => 'boolean',
            'track_clicks' => 'boolean',
            'utm_tags' => 'boolean',
            'schedule_at' => 'date_format:Y-m-d H:i:s',
        ];
    }

    public function template(): Template
    {
        $templateClass = $this->getTemplateClass();

        return $templateClass::find($this->template_id) ?? new $templateClass();
    }
}
