<?php

namespace Spatie\Mailcoach\Http\Api\Requests;

use Illuminate\Validation\Validator;
use Spatie\ValidationRules\Rules\Delimited;

class SendAutomationMailTestRequest extends SendCampaignTestRequest
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
        // override logic in parent
    }

    public function messages()
    {
        return [
            'email.required' => 'You must specify at least one e-mail address.',
            'email.email' => 'Not all the given e-mails are valid.',
        ];
    }
}
