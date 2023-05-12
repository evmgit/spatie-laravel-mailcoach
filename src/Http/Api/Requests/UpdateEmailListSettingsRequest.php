<?php


namespace Spatie\Mailcoach\Http\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Spatie\ValidationRules\Rules\Delimited;

class UpdateEmailListSettingsRequest extends FormRequest
{
    const WELCOME_MAIL_DISABLED = 'do_not_send_welcome_mail';
    const WELCOME_MAIL_DEFAULT_CONTENT = 'send_default_welcome_mail';
    const WELCOME_MAIL_CUSTOM_CONTENT = 'send_custom_welcome_mail';

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
            'transactional_mailer' => [Rule::in(array_keys(config('mail.mailers')))],
            'allow_form_subscriptions' => 'boolean',
            'allowed_form_extra_attributes' => '',
            'requires_confirmation' => 'boolean',
            'allowed_form_subscription_tags' => 'array',
            'redirect_after_subscribed' => '',
            'redirect_after_already_subscribed' => '',
            'redirect_after_subscription_pending' => '',
            'redirect_after_unsubscribed' => '',
            'welcome_mail' => Rule::in([static::WELCOME_MAIL_DISABLED, static::WELCOME_MAIL_DEFAULT_CONTENT, static::WELCOME_MAIL_CUSTOM_CONTENT]),
            'welcome_mail_subject' => 'required_if:welcome_mail,' . static::WELCOME_MAIL_CUSTOM_CONTENT,
            'welcome_mail_content' => 'required_if:welcome_mail,' . static::WELCOME_MAIL_CUSTOM_CONTENT,
            'welcome_mail_delay_in_minutes' => 'nullable|numeric',
            'confirmation_mail' => Rule::in([static::CONFIRMATION_MAIL_DEFAULT, static::CONFIRMATION_MAIL_CUSTOM]),
            'confirmation_mail_subject' => 'required_if:custom_confirmation_mail,' . static::CONFIRMATION_MAIL_CUSTOM,
            'confirmation_mail_content' => 'required_if:custom_confirmation_mail,'. static::CONFIRMATION_MAIL_CUSTOM,
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

    public function sendWelcomeMail(): bool
    {
        return $this->welcome_mail !== static::WELCOME_MAIL_DISABLED;
    }

    public function sendDefaultConfirmationMail(): bool
    {
        return $this->confirmation_mail === static::CONFIRMATION_MAIL_DEFAULT;
    }

    public function messages()
    {
        $customMailRequiredValidationMessage = 'This field is required when using a custom mail';

        return [
            'welcome_mail_subject.required_if' => $customMailRequiredValidationMessage,
            'welcome_mail_content.required_if' => $customMailRequiredValidationMessage,
            'confirmation_mail_subject.required_if' => $customMailRequiredValidationMessage,
            'confirmation_mail_content.required_if' => $customMailRequiredValidationMessage,
        ];
    }
}
