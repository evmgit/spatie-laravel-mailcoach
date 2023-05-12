<?php

namespace Spatie\Mailcoach\Domain\Campaign\Support\Replacers;

use Spatie\Mailcoach\Domain\Campaign\Support\Replacers\Concerns\ReplacesModelAttributes;
use Spatie\Mailcoach\Domain\Shared\Models\Send;

class SubscriberReplacer implements PersonalizedReplacer
{
    use ReplacesModelAttributes;

    public function helpText(): array
    {
        return [
            'subscriber.first_name' => __('The first name of the subscriber'),
            'subscriber.email' => __('The email of the subscriber'),
        ];
    }

    public function replace(string $text, Send $pendingSend): string
    {
        $subscriber = $pendingSend->subscriber;

        return $this->replaceModelAttributes($text, 'subscriber', $subscriber);
    }
}
