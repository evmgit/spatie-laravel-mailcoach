<?php

namespace Spatie\Mailcoach\Http\App\Requests\Campaigns;

use Illuminate\Foundation\Http\FormRequest;
use Spatie\ValidationRules\Rules\Delimited;

class SendCampaignTestRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'emails' => ['required', (new Delimited('email'))->min(1)->max(10)],
        ];
    }

    public function sanitizedEmails(): array
    {
        return array_map('trim', explode(',', $this->emails));
    }

    public function messages()
    {
        return [
            'emails.required' => 'You must specify at least one e-mail address.',
            'emails.email' => 'Not all the given e-mails are valid.',
        ];
    }
}
