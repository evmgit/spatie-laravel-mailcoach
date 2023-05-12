<?php

namespace Spatie\Mailcoach\Http\App\Requests\Automation\Mail;

use Illuminate\Foundation\Http\FormRequest;

class StoredAutomationMailRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'required',
        ];
    }
}
