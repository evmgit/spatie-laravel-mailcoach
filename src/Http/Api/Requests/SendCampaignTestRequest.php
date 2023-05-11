<?php

namespace Spatie\Mailcoach\Http\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Spatie\ValidationRules\Rules\Delimited;

class SendCampaignTestRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => ['required', (new Delimited('email'))->min(1)->max(10)],
        ];
    }

    public function sanitizedEmails(): array
    {
        return array_map('trim', explode(',', $this->email));
    }

    protected function getValidatorInstance()
    {
        return parent::getValidatorInstance()->after(function ($validator) {
            $this->after($validator);
        });
    }

    public function after(Validator $validator)
    {
        /** @var \Spatie\Mailcoach\Domain\Campaign\Models\Campaign $campaign */
        $campaign = $this->route('campaign');

        if (! $campaign->isDraft()) {
            $validator->errors()->add('campaign', 'You cannot send a test mail for a campaign that already was sent.');
        }
    }

    public function messages()
    {
        return [
            'email.required' => 'You must specify at least one e-mail address.',
            'email.email' => 'Not all the given e-mails are valid.',
        ];
    }
}
