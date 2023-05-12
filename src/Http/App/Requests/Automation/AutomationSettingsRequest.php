<?php

namespace Spatie\Mailcoach\Http\App\Requests\Automation;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Support\Segments\EverySubscriberSegment;
use Spatie\Mailcoach\Domain\Audience\Support\Segments\SubscribersWithTagsSegment;
use Spatie\Mailcoach\Domain\Automation\Support\Triggers\AutomationTrigger;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class AutomationSettingsRequest extends FormRequest
{
    use UsesMailcoachModels;

    public function rules(): array
    {
        $rules = [
            'name' => 'required',
            'email_list_id' => Rule::exists($this->getEmailListTableName(), 'id'),
            'segment' => [Rule::in(['entire_list', 'segment'])],
            'segment_id' => ['required_if:segment,tag_segment'],
            'trigger' => ['required', Rule::in(config('mailcoach.automation.flows.triggers'))],
        ];

        if ($this->has('trigger')) {
            $rules = array_merge($rules, $this->get('trigger')::rules());
        }

        return $rules;
    }

    public function getSegmentClass(): string
    {
        /** @var \Spatie\Mailcoach\Domain\Campaign\Models\Campaign $campaign */
        $automation = $this->route()->parameter('automation');

        if ($automation->usingCustomSegment()) {
            return $automation->segment_class;
        }

        if ($this->segment === 'entire_list') {
            return EverySubscriberSegment::class;
        }

        return SubscribersWithTagsSegment::class;
    }

    public function emailList(): ?EmailList
    {
        if (! $this->email_list_id) {
            return null;
        }

        return $this->getEmailListClass()::find($this->email_list_id);
    }

    public function trigger(): AutomationTrigger
    {
        return $this->get('trigger')::make($this->all());
    }
}
