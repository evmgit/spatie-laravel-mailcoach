<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Actions;

use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\Enums\ActionCategoryEnum;

class UnsubscribeAction extends AutomationAction
{
    public static function getName(): string
    {
        return (string) __('Unsubscribe');
    }

    public static function getCategory(): ActionCategoryEnum
    {
        return ActionCategoryEnum::react();
    }

    public function run(Subscriber $subscriber): void
    {
        $subscriber->unsubscribe();
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
