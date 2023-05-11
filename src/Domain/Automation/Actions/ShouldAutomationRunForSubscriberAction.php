<?php

namespace Spatie\Mailcoach\Domain\Automation\Actions;

use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Models\Action;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class ShouldAutomationRunForSubscriberAction
{
    use UsesMailcoachModels;

    public function execute(Automation $automation, Subscriber $subscriber): bool
    {
        $currentActions = $subscriber->currentActions($automation);

        if ($currentActions->count()) {
            if (! $automation->repeat_enabled) {
                return false;
            }

            if ($automation->repeat_only_after_halt) {
                return $currentActions->count() === $currentActions
                        ->filter(fn (Action $action) => $action->pivot->halted_at)
                        ->count();
            }
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
