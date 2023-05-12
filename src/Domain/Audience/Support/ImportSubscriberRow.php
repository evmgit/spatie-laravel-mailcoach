<?php

namespace Spatie\Mailcoach\Domain\Audience\Support;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Spatie\Mailcoach\Domain\Audience\Enums\SubscriptionStatus;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;

class ImportSubscriberRow
{
    protected EmailList $emailList;

    protected array $values;

    public function __construct(EmailList $emailList, array $values)
    {
        $this->emailList = $emailList;

        $this->values = array_map('trim', array_change_key_case($values, CASE_LOWER));
    }

    public function hasValidEmail(): bool
    {
        $validator = Validator::make($this->values, ['email' => 'required|email']);

        return ! $validator->fails();
    }

    public function hasUnsubscribed(): bool
    {
        $subscriptionStatus = $this->emailList->getSubscriptionStatus($this->values['email']);

        return $subscriptionStatus === SubscriptionStatus::UNSUBSCRIBED;
    }

    public function getAllValues(): array
    {
        return $this->values;
    }

    public function getEmail(): string
    {
        return $this->values['email'] ?? '';
    }

    public function getAttributes(): array
    {
        return [
            'first_name' => $this->values['first_name'] ?? '',
            'last_name' => $this->values['last_name'] ?? '',
        ];
    }

    public function getExtraAttributes(): array
    {
        return collect($this->values)
            ->reject(fn ($_value, string $key) => in_array($key, ['email', 'first_name', 'last_name']))
            ->map(fn ($value, string $key) => [$key, $value])
            ->reduce(function (array $result, $keyValuePair) {
                [$key, $value] = $keyValuePair;

                $key = Str::replaceFirst('extra_attributes.', '', $key);

                $result[$key] = $value;

                return $result;
            }, []);
    }

    public function tags(): ?array
    {
        if (! array_key_exists('tags', $this->values)) {
            return null;
        }

        $tags = explode(';', $this->values['tags']);

        $sanitizedTags = array_map('trim', $tags);

        return array_filter($sanitizedTags);
    }
}
