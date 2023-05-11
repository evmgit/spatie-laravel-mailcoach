<?php

namespace Spatie\Mailcoach\Http\Front\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class CreateSubscriptionRequest extends FormRequest
{
    use UsesMailcoachModels;

    private ?EmailList $emailList = null;

    public function rules()
    {
        return [
            'email' => ['required', 'email:rfc,dns'],
            'first_name' => ['nullable', 'string'],
            'last_name' => ['nullable', 'string'],
            'redirect_after_subscribed' => 'nullable',
            'redirect_after_already_subscribed' => 'nullable',
            'redirect_after_subscription_pending' => 'nullable',
            'tags' => 'nullable',
            'attributes' => 'nullable',
        ];
    }

    public function subscriberAttributes(): array
    {
        return Arr::except($this->validated(), [
            'email',
            'redirect_after_subscribed',
            'redirect_after_already_subscribed',
            'redirect_after_subscription_pending',
            'tags',
            'attributes',
        ]);
    }

    public function attributes()
    {
        $allowedEmailListAttributes = $this->emailList()->allowedFormExtraAttributes();

        $attributes = [];

        foreach ($this->get('attributes', []) as $key => $attributeValue) {
            if (in_array(trim($key), $allowedEmailListAttributes)) {
                $attributes[$key] = $attributeValue;
            }
        }

        return $attributes;
    }

    public function tags(): array
    {
        $tags = explode(';', $this->tags);

        $tags = array_map('trim', $tags);

        $allowedEmailListTags = $this->emailList()->allowedFormSubscriptionTags()->pluck('name')->toArray();

        $tags = array_filter($tags, fn (string $tag) => in_array($tag, $allowedEmailListTags));

        return array_filter($tags);
    }

    public function emailList(): EmailList
    {
        if (! $this->emailList) {
            $this->emailList = self::getEmailListClass()::query()
                ->where('uuid', $this->route()->parameter('emailListUuid'))
                ->where('allow_form_subscriptions', true)
                ->firstOrFail();
        }

        return $this->emailList;
    }
}
