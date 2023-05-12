<?php

namespace Spatie\Mailcoach\Domain\Shared\Actions;

class AddUtmTagsToUrlAction
{
    public function execute(string $url, string $campaignName): string
    {
        if (str_starts_with($url, '::')) {
            return $url;
        }

        $tags = [
            'utm_source' => 'newsletter',
            'utm_medium' => 'email',
            'utm_campaign' => $campaignName,
        ];

        $parsedUrl = parse_url($url);
        $parsedQuery = $tags;

        if (! isset($parsedUrl['host'])) {
            return $url;
        }

        if (! empty($parsedUrl['query'])) {
            parse_str($parsedUrl['query'], $parsedQuery);
            foreach ($tags as $key => $value) {
                if (empty($parsedQuery[$key])) {
                    $parsedQuery[$key] = $value;
                }
            }
        }

        $query = http_build_query($parsedQuery);
        $path = $parsedUrl['path'] ?? '';

        return "{$parsedUrl['scheme']}://{$parsedUrl['host']}{$path}?{$query}";
    }
}
