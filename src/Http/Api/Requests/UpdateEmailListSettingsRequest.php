<?php

namespace Spatie\Mailcoach\Http\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Spatie\ValidationRules\Rules\Delimited;

class UpdateEmailListSettingsRequest extends FormRequest
{
    const CONFIRMATION_MAIL_DEFAULT = 'send_default_confirmation_mail';

    const CONFIRMATION_MAIL_CUSTOM = 'send_custom_confirmation_mail';

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

            'campaign_mailer' => [Rule::in(array_keys(config('mail.mailers')))],
            'automation_mailer' => [Rule::in(array_keys(config('mail.mailers')))],
            'transactional_mailer' => [Rule::in(array_keys(config('mail.mailers')))],
            'allow_form_subscriptions' => 'boolean',
            'allowed_form_extra_attributes' => '',
            'requires_confirmation' => 'boolean',
            'allowed_form_subscription_tags' => 'array',
            'honeypot_field' => ['nullable', 'string'],
            'redirect_after_subscribed' => '',
            'redirect_after_already_subscribed' => '',
            'redirect_after_subscription_pending' => '',
            'redirect_after_unsubscribed' => '',
            'confirmation_mail' => Rule::in([static::CONFIRMATION_MAIL_DEFAULT, static::CONFIRMATION_MAIL_CUSTOM]),
            'confirmation_mail_uuid' => 'required_if:custom_confirmation_mail,'.static::CONFIRMATION_MAIL_CUSTOM,
        ];
    }

    public function allowedFormSubscriptionTags(): Collection
    {
        $allowedTagNames = $this->allowed_form_subscription_tags ?? [];

        if (count($allowedTagNames) === 0) {
            return collect();
        }

        /** @var \Spatie\Mailcoach\Domain\Audience\Models\EmailList $emailList */
        $emailList = $this->route()->parameter('emailList');

        return $emailList->tags()->whereIn('name', $allowedTagNames)->get();
    }

    public function sendDefaultConfirmationMail(): bool
    {
        return $this->confirmation_mail === static::CONFIRMATION_MAIL_DEFAULT;
    }

    public function messages()
    {
        $customMailRequiredValidationMessage = 'This field is required when using a custom mail';

        return [
            'confirmation_mail_subject.required_if' => $customMailRequiredValidationMessage,
            'confirmation_mail_content.required_if' => $customMailRequiredValidationMessage,
        ];
    }
}
