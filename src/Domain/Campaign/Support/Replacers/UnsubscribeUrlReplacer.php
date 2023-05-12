<?php

namespace Spatie\Mailcoach\Domain\Campaign\Support\Replacers;

use Spatie\Mailcoach\Domain\Shared\Models\Send;

class UnsubscribeUrlReplacer implements PersonalizedReplacer
{
    public function helpText(): array
    {
        return [
            'unsubscribeUrl' => __('The URL where users can unsubscribe'),
            'unsubscribeTag::your tag' => __('The URL where users can be removed from a specific tag'),
        ];
    }

    public function replace(string $text, Send $pendingSend): string
    {
        $unsubscribeUrl = $pendingSend->subscriber->unsubscribeUrl($pendingSend);

        $text = str_ireplace('::unsubscribeUrl::', $unsubscribeUrl, $text);

        preg_match_all('/::unsubscribeTag::([^:]*)::/', $text, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            [$key, $tag] = $match;

            $unsubscribeTagUrl = $pendingSend->subscriber->unsubscribeTagUrl($tag);

            $text = str_ireplace($key, $unsubscribeTagUrl, $text);
        }

        return $text;
    }
}
