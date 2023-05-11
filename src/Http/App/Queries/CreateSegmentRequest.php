<?php

namespace Spatie\Mailcoach\Http\App\Queries;

use Illuminate\Foundation\Http\FormRequest;

class CreateSegmentRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required',
        ];
    }
}
