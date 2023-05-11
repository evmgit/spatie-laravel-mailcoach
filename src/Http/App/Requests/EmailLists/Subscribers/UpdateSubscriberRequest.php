<?php

namespace Spatie\Mailcoach\Http\App\Requests\EmailLists\Subscribers;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class UpdateSubscriberRequest extends FormRequest
{
    use UsesMailcoachModels;

    public function rules(): array
    {
        return [
            'email' => ['email:rfc', $this->getUniqueRule()],
            'first_name' => 'nullable|string',
            'last_name' => 'nullable|string',
            'tags' => 'array',
            'extra_attributes' => ['nullable', 'array'],
        ];
    }

    public function subscriberAttributes()
    {
        return Arr::except($this->validated(), ['tags']);
    }

    protected function getUniqueRule(): Unique
    {
        $emailList = $this->route('emailList');

        $subscriber = $this->route('subscriber');

        if (is_string($subscriber)) {
            $subscriber = self::getSubscriberClass()::findOrFail($subscriber);
        }

        if (! $emailList) {
            $emailList = $subscriber->emailList;
        }

        $rule = Rule::unique($this->getSubscriberTableName(), 'email')->where('email_list_id', $emailList->id);

        $rule->ignore($subscriber->id);

        return $rule;
    }

    public function messages()
    {
        return [
            'email.unique' => 'There already is a subscriber on this list with this email.',
        ];
    }
}
