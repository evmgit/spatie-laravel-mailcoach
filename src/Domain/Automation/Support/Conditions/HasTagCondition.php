<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Conditions;

use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;

class HasTagCondition implements Condition
{
    public function __construct(
        private Automation $automation,
        private Subscriber $subscriber,
        private array $data,
    ) {
    }

    public static function getName(): string
    {
        return (string) __mc('Has tag');
    }

    public static function getDescription(array $data): string
    {
        return (string) __mc(':tag', ['tag' => $data['tag']]);
    }

    public static function rules(): array
    {
        return [
            'tag' => 'required',
        ];
    }

    public function check(): bool
    {
        return $this->subscriber->hasTag($this->data['tag']);
    }
}
