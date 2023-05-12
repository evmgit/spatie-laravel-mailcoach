<?php

namespace Spatie\Mailcoach\Http\App\Requests\Automation;

use Illuminate\Foundation\Http\FormRequest;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class RunAutomationRequest extends FormRequest
{
    use UsesMailcoachModels;

    public function rules(): array
    {
        return [
            'interval' => ['required'],
        ];
    }
}
