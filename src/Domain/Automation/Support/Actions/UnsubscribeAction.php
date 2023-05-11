<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Actions;

use Spatie\Mailcoach\Domain\Automation\Models\ActionSubscriber;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\Enums\ActionCategoryEnum;

class UnsubscribeAction extends AutomationAction
{
    public static function getName(): string
    {
        return (string) __mc('Unsubscribe');
    }

    public static function getCategory(): ActionCategoryEnum
    {
        return ActionCategoryEnum::React;
    }

    public function run(ActionSubscriber $actionSubscriber): void
    {
        $actionSubscriber->subscriber->unsubscribe();
    }

    public function shouldHalt(ActionSubscriber $actionSubscriber): bool
    {
        return true;
    }

    public function shouldContinue(ActionSubscriber $actionSubscriber): bool
    {
        return false;
    }
}
