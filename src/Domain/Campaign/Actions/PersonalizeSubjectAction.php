<?php

namespace Spatie\Mailcoach\Domain\Campaign\Actions;

use Spatie\Mailcoach\Domain\Campaign\Support\Replacers\PersonalizedReplacer;
use Spatie\Mailcoach\Domain\Shared\Models\Send;

class PersonalizeSubjectAction
{
    public function execute(string $subject, Send $pendingSend): string
    {
        /** @var \Spatie\Mailcoach\Domain\Audience\Models\Subscriber $subscriber */
        $subscriber = $pendingSend->subscriber;

        $subject = str_ireplace('::sendUuid::', $pendingSend->uuid, $subject);
        $subject = str_ireplace('::subscriber.uuid::', $subscriber->uuid, $subject);

        return collect(config('mailcoach.campaigns.replacers'))
            ->map(fn (string $className) => resolve($className))
            ->filter(fn (object $class) => $class instanceof PersonalizedReplacer)
            ->reduce(fn (string $subject, PersonalizedReplacer $replacer) => $replacer->replace($subject, $pendingSend), $subject);
    }
}
