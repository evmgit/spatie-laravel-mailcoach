<?php

namespace Spatie\Mailcoach\Domain\Campaign\Exceptions;

use Exception;
use Livewire\Component;

class InvalidConfig extends Exception
{
    public static function invalidAction(string $actionName, string $configuredClass, string $actionClass): self
    {
        return new static("The class currently specified in the `mailcoach.campaigns.actions.{$actionName}` key '{$configuredClass}' should be or extend `{$actionClass}`.");
    }

    public static function invalidLivewireComponent(string $componentName, string $class): self
    {
        $componentClass = Component::class;

        return new static("The class currently specified in the `mailcoach.livewire.{$componentName}` key '{$class}' should be or extend `{$componentClass}`.");
    }
}
