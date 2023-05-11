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

        if ($mail->add_subscriber_tags) {
            $subscriber->addTag("automation-mail-{$mail->uuid}-clicked", TagType::Mailcoach);
        }

        if ($mail->add_subscriber_link_tags) {
            $hash = LinkHasher::hash(
                sendable: $event->automationMailClick->link->automationMail,
                url: $event->automationMailClick->link->url,
            );

            $subscriber->addTag($hash, TagType::Mailcoach);
        }
    }
}
