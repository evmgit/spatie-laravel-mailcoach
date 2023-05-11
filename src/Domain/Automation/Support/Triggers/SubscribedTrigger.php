<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Triggers;

use Spatie\Mailcoach\Domain\Audience\Events\SubscribedEvent;

class SubscribedTrigger extends AutomationTrigger implements TriggeredByEvents
{
    public static function getName(): string
    {
        return (string) __mc('When a user subscribes');
    }

    public function subscribe($events): void
    {
        $events->listen(
            SubscribedEvent::class,
            function ($event) {
                $this->runAutomation($event->subscriber);
            }
        );
    }
}
