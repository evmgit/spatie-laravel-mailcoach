<?php

namespace Spatie\Mailcoach\Domain\Automation\Listeners;

use Spatie\Mailcoach\Domain\Automation\Events\AutomationMailOpenedEvent;
use Spatie\Mailcoach\Domain\Campaign\Enums\TagType;

class AddAutomationMailOpenedTag
{
    public function handle(AutomationMailOpenedEvent $event)
    {
        $campaign = $event->automationMailOpen->send->automationMail;
        $subscriber = $event->automationMailOpen->send->subscriber;

        $subscriber->addTag("automation-mail-{$campaign->id}-opened", TagType::MAILCOACH);
    }
}
