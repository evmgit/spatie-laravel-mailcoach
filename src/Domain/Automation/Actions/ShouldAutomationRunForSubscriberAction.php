<?php


namespace Spatie\Mailcoach\Domain\Automation\Actions;

use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class ShouldAutomationRunForSubscriberAction
{
    use UsesMailcoachModels;

    public function execute(Automation $automation, Subscriber $subscriber): bool
    {
        if ($subscriber->inAutomation($automation)) {
            return false;
        }

        if (! $subscriber->isSubscribed()) {
            return false;
        }

        if (! $automation
            ->newSubscribersQuery()
            ->where("{$this->getSubscriberTableName()}.id", $subscriber->id)
            ->exists()
        ) {
            return false;
        }

        return true;
    }
}
