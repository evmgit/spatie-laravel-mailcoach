<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Triggers;

class NoTrigger extends AutomationTrigger
{
    public static function getName(): string
    {
        return (string) __('No trigger');
    }

    public static function getComponent(): ?string
    {
        return 'no-trigger';
    }
}
