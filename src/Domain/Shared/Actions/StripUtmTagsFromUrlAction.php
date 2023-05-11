<?php

namespace Spatie\Mailcoach\Domain\Shared\Actions;

class StripUtmTagsFromUrlAction
{
    public function execute(string $url): string
    {
        $parsedUrl = parse_url($url);

        if (! isset($parsedUrl['query'])) {
            return $url;
        }

        parse_str($parsedUrl['query'], $query);

        unset($query['utm_source']);
        unset($query['utm_medium']);
        unset($query['utm_campaign']);

        $query = http_build_query($query);
        $path = $parsedUrl['path'] ?? '';

        if ($query) {
            return "{$parsedUrl['scheme']}://{$parsedUrl['host']}{$path}?{$query}";
        }

        return "{$parsedUrl['scheme']}://{$parsedUrl['host']}{$path}";
    }
}
