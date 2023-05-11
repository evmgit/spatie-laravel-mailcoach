<?php

namespace Spatie\Mailcoach\Domain\Settings\Support;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Spatie\Mailcoach\Enums\SubscriptionStatus;
use Spatie\Mailcoach\Models\EmailList;

class ImportSubscriberRow
{
    private EmailList $emailList;

    private array $values = [];

    public function __construct(EmailList $emailList, array $values)
    {
        $this->emailList = $emailList;

        $this->values = $values;
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
            ->reject(fn ($value, string $key) => in_array($key, ['email', 'first_name', 'last_name']))
            ->map(fn ($value, string $key) => [$key, $value])
            ->reduce(function (array $result, $keyValuePair) {
                [$key, $value] = $keyValuePair;

                $key = Str::replaceFirst('extra_attributes.', '', $key);

                $result[$key] = $value;

                return $result;
            }, []);
    }
}
