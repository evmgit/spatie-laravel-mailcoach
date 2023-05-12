<?php

namespace Spatie\Mailcoach\Domain\Automation\Listeners;

use Spatie\Mailcoach\Domain\Automation\Events\AutomationMailLinkClickedEvent;
use Spatie\Mailcoach\Domain\Campaign\Enums\TagType;
use Spatie\Mailcoach\Domain\Shared\Support\LinkHasher;

class AddAutomationMailClickedTag
{
    public function handle(AutomationMailLinkClickedEvent $event)
    {
        $mail = $event->automationMailClick->link->automationMail;
        $subscriber = $event->automationMailClick->send->subscriber;

        $hash = LinkHasher::hash(
            $event->automationMailClick->link->automationMail,
            $event->automationMailClick->link->url,
            'clicked'
        );

        $subscriber->addTag("automation-mail-{$mail->id}-clicked", TagType::MAILCOACH);
        $subscriber->addTag($hash, TagType::MAILCOACH);
    }
}
