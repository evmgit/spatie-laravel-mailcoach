<?php

namespace Spatie\Mailcoach\Http\App\Requests\EmailLists\Settings;

use Illuminate\Foundation\Http\FormRequest;
use Spatie\ValidationRules\Rules\Delimited;

class UpdateEmailListGeneralSettingsRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required',
            'default_from_email' => 'required|email:rfc',
            'default_from_name' => '',
            'default_reply_to_email' => 'nullable|email:rfc',
            'default_reply_to_name' => '',

            'campaigns_feed_enabled' => 'boolean',

            'report_campaign_sent' => 'boolean',
            'report_campaign_summary' => 'boolean',
            'report_email_list_summary' => 'boolean',

            'report_recipients' => [
                new Delimited('email'),
                'required_if:report_email_list_summary,1',
                'required_if:report_campaign_sent,1',
                'required_if:report_campaign_summary,1',
            ],
        ];
    }
}
