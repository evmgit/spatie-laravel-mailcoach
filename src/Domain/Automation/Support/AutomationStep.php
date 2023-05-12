<?php


namespace Spatie\Mailcoach\Domain\Automation\Support;

use Illuminate\Support\Str;

abstract class AutomationStep
{
    public string $uuid;

    public function __construct(?string $uuid = null)
    {
        if (is_null($uuid)) {
            $this->uuid = Str::uuid()->toString();
        }
    }

    public static function make(array $data)
    {
        return new static();
    }

    public function toArray(): array
    {
        return [];
    }

    public static function getName(): string
    {
        return static::class;
    }

    public function getDescription(): string
    {
        return '';
    }

    public static function getComponent(): ?string
    {
        return null;
    }
}
