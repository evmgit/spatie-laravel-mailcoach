<?php

namespace Spatie\Mailcoach\Http\App\Requests\Campaigns;

use Illuminate\Foundation\Http\FormRequest;
use Spatie\Mailcoach\Domain\Campaign\Rules\HtmlRule;

class UpdateCampaignContentRequest extends FormRequest
{
    public function rules(): array
    {
        $template = $this->route('campaign')?->template;

        if (! $template?->containsPlaceHolders() ?? false) {
            return [
                'html' => ['required', new HtmlRule()],
                'structured_html' => ['nullable'],
            ];
        }

        return [];
    }

    public function templateField(mixed $placeHolderName): string
    {
        $formFieldName = 'field_'.$placeHolderName;

        return $this->$formFieldName ?? '';
    }
}
