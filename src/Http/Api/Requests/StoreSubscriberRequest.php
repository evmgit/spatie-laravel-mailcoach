<?php

namespace Spatie\Mailcoach\Http\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Rules\EmailListSubscriptionRule;

class StoreSubscriberRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => ['required', 'email:rfc', new EmailListSubscriptionRule($this->emailList())],
            'first_name' => ['nullable', 'string'],
            'last_name' => ['nullable', 'string'],
            'extra_attributes' => ['nullable', 'array'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string'],
        ];
    }

    public function emailList(): EmailList
    {
        return request()->route()->parameter('email_list');
    }

    public function subscriberAttributes(): array
    {
        return [
            'first_name' => $this->input('first_name'),
            'last_name' => $this->input('last_name'),
            'extra_attributes' => $this->input('extra_attributes'),
        ];
    }

    public function messages()
    {
        return [
            'email.unique' => 'There already is a subscriber with this email.',
        ];
    }
}
