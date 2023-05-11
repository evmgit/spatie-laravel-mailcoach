<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Actions\Enums;

enum ActionCategoryEnum: string
{
    case Tags = 'tags';
    case Check = 'check';
    case Pause = 'pause';
    case React = 'react';

    public function label(): string
    {
        return match ($this) {
            self::Tags => __mc('Tags'),
            self::Check => __mc('Check'),
            self::Pause => __mc('Pause'),
            self::React => __mc('React'),
        };
    }

    protected static function labels(): array
    {
        return [
            'tags' => 'Tags',
            'check' => 'Route',
            'pause' => 'Pause',
            'react' => 'Respond',
        ];
    }

    public static function icons(): array
    {
        return [
            'tags' => 'fa-tag',
            'check' => 'fa-random',
            'pause' => 'fa-clock',
            'react' => 'fa-cogs',
        ];
    }
}
