<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Replacers;

use Spatie\Mailcoach\Domain\Shared\Models\Send;

class UnsubscribeUrlReplacer implements PersonalizedReplacer
{
    public function helpText(): array
    {
        return [
            'unsubscribeUrl' => __mc('The URL where users can unsubscribe'),
            'unsubscribeTag::your tag' => __mc('The URL where users can be removed from a specific tag'),
            'preferencesUrl' => __mc('The URL where users can manage their preferences'),
        ];
    }

    public function replace(string $text, Send $pendingSend): string
    {
        $unsubscribeUrl = $pendingSend->subscriber->unsubscribeUrl($pendingSend);
        $preferencesUrl = $pendingSend->subscriber->preferencesUrl($pendingSend);

        $text = str_ireplace('::unsubscribeUrl::', $unsubscribeUrl, $text);
        $text = str_ireplace(urlencode('::unsubscribeUrl::'), $unsubscribeUrl, $text);
        $text = str_ireplace('::preferencesUrl::', $preferencesUrl, $text);
        $text = str_ireplace(urlencode('::preferencesUrl::'), $preferencesUrl, $text);

        $pattern = <<<'REGEXP'
            /
            (?:::|%3A%3A)                   # "::" or urlencoded "%3A%3A"
            unsubscribeTag(?:::|%3A%3A)     # "unsubscribeTag::" or urlencoded "unsubscribeTag%3A%3A"
            ((?!::|%3A%3A).*)               # Anything but "::" or "%3A%3A"
            (?:::|%3A%3A)                   # "::" or urlencoded "%3A%3A"
            /ix
        REGEXP;

        preg_match_all($pattern, $text, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            [$key, $tag] = $match;

            $unsubscribeTagUrl = $pendingSend->subscriber->unsubscribeTagUrl(urldecode($tag), $pendingSend);

            $text = str_ireplace($key, $unsubscribeTagUrl, $text);
        }

        return $text;
    }
}
