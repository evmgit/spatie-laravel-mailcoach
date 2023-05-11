<?php

namespace Spatie\Mailcoach\Domain\Shared\Support;

use Illuminate\Support\Str;
use Spatie\Mailcoach\Domain\Shared\Models\Sendable;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class LinkHasher
{
    use UsesMailcoachModels;

    public static function hash(Sendable $sendable, string $url, string $type = 'clicked'): string
    {
        $prefix = match ($sendable::class) {
            static::getCampaignClass() => 'campaign',
            static::getAutomationMailClass() => 'automation-mail',
        };

        $sendablePart = "{$prefix}-{$sendable->uuid}-{$type}";

        $humanReadablePart = self::getHumanReadablePart($url);

        $randomPart = substr(md5($url), 0, 8);

        return "{$sendablePart}-{$humanReadablePart}-{$randomPart}";
    }

    protected static function getHumanReadablePart(string $url)
    {
        $url = Str::after($url, '://');

        $slug = str_replace(['.'], '-', $url);

        $slug = Str::slug($slug);

        return Str::limit($slug, 30, '');
    }
}
