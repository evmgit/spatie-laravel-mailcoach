<?php

namespace Spatie\Mailcoach\Http\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubscriberImportRequest extends FormRequest
{
    public function rules()
    {
        return [
            'subscribers_csv' => 'required',
            'email_list_uuid' => 'required',
            'subscribe_unsubscribed' => 'boolean',
            'unsubscribe_others' => 'boolean',
            'replace_tags' => 'boolean',
        ];
    }
}
