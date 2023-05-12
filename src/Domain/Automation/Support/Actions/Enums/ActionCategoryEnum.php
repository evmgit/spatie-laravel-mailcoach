<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Actions\Enums;

use Spatie\Enum\Enum;

/**
 * @method static self tags()
 * @method static self check()
 * @method static self pause()
 * @method static self react()
 */
class ActionCategoryEnum extends Enum
{
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
