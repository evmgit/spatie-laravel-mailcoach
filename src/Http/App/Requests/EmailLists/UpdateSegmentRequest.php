<?php

namespace Spatie\Mailcoach\Http\App\Requests\EmailLists;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSegmentRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required',
            'positive_tags_operator' => [Rule::in(['any', 'all'])],
            'positive_tags.*' => [Rule::in($this->emailListTagNames())],
            'negative_tags_operator' => [Rule::in(['any', 'all'])],
            'negative_tags.*' => [Rule::in($this->emailListTagNames())],
        ];
    }

    public function allPositiveTagsRequired(): bool
    {
        return $this->positive_tags_operator === 'all';
    }

    public function allNegativeTagsRequired(): bool
    {
        return $this->negative_tags_operator === 'all';
    }

    protected function emailListTagNames(): array
    {
        $emailList = $this->route()->parameter('emailList');

        return $emailList->tags()->pluck('name')->toArray();
    }
}
