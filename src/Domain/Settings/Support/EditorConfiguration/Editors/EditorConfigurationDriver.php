<?php

namespace Spatie\Mailcoach\Domain\Settings\Support\EditorConfiguration\Editors;

use Illuminate\Contracts\Config\Repository;

abstract class EditorConfigurationDriver
{
    abstract public static function label(): string;

    /** @return class-string<\Spatie\Mailcoach\Http\App\Livewire\EditorComponent> */
    abstract public function getClass(): string;

    public function validationRules(): array
    {
        return [];
    }

    public function defaults()
    {
        return [];
    }

    public function registerConfigValues(Repository $config, array $values): void
    {
    }

    public static function supportsContent(): bool
    {
        return (new static())->getClass()::$supportsContent;
    }

    public static function supportsTemplates(): bool
    {
        return (new static())->getClass()::$supportsTemplates;
    }

    public static function settingsPartial(): ?string
    {
        return null;
    }
}
