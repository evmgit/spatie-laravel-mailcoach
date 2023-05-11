<?php

namespace Spatie\Mailcoach\Domain\Settings\Support\Concerns;

use Exception;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

trait UsesSettings
{
    use UsesMailcoachModels;

    public function put(array $values): self
    {
        self::getSettingClass()::setValues($this->getKeyName(), $values);

        return $this;
    }

    public function merge(array $values): self
    {
        $allValues = array_merge($this->all(), $values);

        self::getSettingClass()::setValues($this->getKeyName(), $allValues);

        return $this;
    }

    public function all(): array
    {
        return self::getSettingClass()::where('key', $this->getKeyName())->first()?->allValues() ?? [];
    }

    public function empty(): self
    {
        self::getSettingClass()::where('key')->delete();

        return $this;
    }

    public function __get(string $property)
    {
        return $this->get($property);
    }

    public function get(string $property, mixed $default = null): mixed
    {
        try {
            return self::getSettingClass()::where('key', $this->getKeyName())->first()?->getValue($property) ?? $default;
        } catch (Exception) {
            return $default;
        }
    }

    abstract public function getKeyName(): string;
}
