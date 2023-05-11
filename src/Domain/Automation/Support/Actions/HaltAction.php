<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Actions;

use Spatie\Mailcoach\Domain\Automation\Models\ActionSubscriber;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\Enums\ActionCategoryEnum;

class HaltAction extends AutomationAction
{
    public static function getCategory(): ActionCategoryEnum
    {
        return ActionCategoryEnum::Pause;
    }

    public static function getName(): string
    {
        return (string) __mc('Halt the automation');
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
