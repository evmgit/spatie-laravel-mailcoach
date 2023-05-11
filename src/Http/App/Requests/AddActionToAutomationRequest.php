<?php

namespace Spatie\Mailcoach\Http\App\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddActionToAutomationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'action' => 'required',
        ];
    }
}
