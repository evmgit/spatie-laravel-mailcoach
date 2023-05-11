<?php

namespace Spatie\Mailcoach\Http\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AppendSubscriberImportRequest extends FormRequest
{
    public function rules()
    {
        return [
            'subscribers_csv' => 'required',
        ];
    }
}
