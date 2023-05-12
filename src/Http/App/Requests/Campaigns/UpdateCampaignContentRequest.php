<?php

namespace Spatie\Mailcoach\Http\App\Requests\Campaigns;

use Illuminate\Foundation\Http\FormRequest;
use Spatie\Mailcoach\Domain\Campaign\Rules\HtmlRule;

class UpdateCampaignContentRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'html' => ['required', new HtmlRule()],
            'structured_html' => ['nullable'],
        ];
    }
}
