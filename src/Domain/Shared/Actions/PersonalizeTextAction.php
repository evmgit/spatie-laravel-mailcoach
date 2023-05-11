<?php

namespace Spatie\Mailcoach\Domain\Shared\Actions;

use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Automation\Support\Replacers\PersonalizedReplacer as PersonalizedAutomationReplacer;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Campaign\Support\Replacers\PersonalizedReplacer as PersonalizedCampaignReplacer;
use Spatie\Mailcoach\Domain\Shared\Models\Send;

class PersonalizeTextAction
{
    public function execute(?string $text, Send $pendingSend): string
    {
        $text ??= '';

        /** @var Subscriber $subscriber */
        $subscriber = $pendingSend->subscriber;

        $text = str_ireplace('::sendUuid::', $pendingSend->uuid, $text);
        $text = str_ireplace('::subscriber.uuid::', $subscriber->uuid, $text);

        if (! $sendable = $pendingSend->getSendable()) {
            return $text;
        }

        return match (true) {
            $sendable instanceof Campaign => collect(config('mailcoach.campaigns.replacers'))
                ->map(fn (string $className) => resolve($className))
                ->filter(fn (object $class) => $class instanceof PersonalizedCampaignReplacer)
                ->reduce(fn (string $text, PersonalizedCampaignReplacer $replacer) => $replacer->replace($text, $pendingSend), $text),
            $sendable instanceof AutomationMail => collect(config('mailcoach.automation.replacers'))
                ->map(fn (string $className) => resolve($className))
                ->filter(fn (object $class) => $class instanceof PersonalizedAutomationReplacer)
                ->reduce(fn (string $text, PersonalizedAutomationReplacer $replacer) => $replacer->replace($text, $pendingSend), $text),
            default => $text,
        };
    }
}
