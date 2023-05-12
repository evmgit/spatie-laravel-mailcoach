<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Actions;

use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\Enums\ActionCategoryEnum;

class HaltAction extends AutomationAction
{
    public static function getCategory(): ActionCategoryEnum
    {
        return ActionCategoryEnum::pause();
    }

    public static function getName(): string
    {
        return (string) __('Halt the automation');
    }

    public function shouldHalt(Subscriber $subscriber): bool
    {
        return true;
    }

    public function shouldContinue(Subscriber $subscriber): bool
    {
        return false;
    }
}
