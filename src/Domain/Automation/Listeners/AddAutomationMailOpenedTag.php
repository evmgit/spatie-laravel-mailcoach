<?php

namespace Spatie\Mailcoach\Domain\Automation\Listeners;

use Spatie\Mailcoach\Domain\Automation\Events\AutomationMailOpenedEvent;
use Spatie\Mailcoach\Domain\Campaign\Enums\TagType;

class AddAutomationMailOpenedTag
{
    public function handle(AutomationMailOpenedEvent $event)
    {
        $automationMail = $event->automationMailOpen->send->automationMail;

        if (! $automationMail->add_subscriber_tags) {
            return;
        }

        $subscriber = $event->automationMailOpen->send->subscriber;

        $subscriber->addTag("automation-mail-{$automationMail->uuid}-opened", TagType::Mailcoach);
    }
}
