<?php

namespace Spatie\Mailcoach\Http\App\Requests\EmailLists;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateTagRequest extends FormRequest
{
    public function rules()
    {
        $emailList = $this->route()->parameter('emailList');

        return [
            'name' => ['required', Rule::unique('mailcoach_tags')->where('email_list_id', $emailList->id)],
        ];
    }
}
