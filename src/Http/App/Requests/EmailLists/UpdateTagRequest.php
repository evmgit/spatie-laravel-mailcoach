<?php

namespace Spatie\Mailcoach\Http\App\Requests\EmailLists;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTagRequest extends FormRequest
{
    public function rules(): array
    {
        $emailList = $this->route()->parameter('emailList');

        $tag = $this->route()->parameter('tag');

        return [
            'name' => [
                'required',
                Rule::unique('mailcoach_tags', 'name')
                    ->where('email_list_id', $emailList)
                    ->ignore($tag->id),
            ],
        ];
    }
}
