<?php

namespace Spatie\Mailcoach\Http\App\ViewComposers;

use Illuminate\Support\HtmlString;
use Illuminate\View\View;

class WebsiteStyleComposer
{
    public function compose(View $view)
    {
        // Pass as a closure so we don't do any unnecessary work when users provide their own CSS.
        $view->with('css', function () {
            return new HtmlString($this->minifyCss(
                file_get_contents(__DIR__.'/../../../../resources/css/website.css')
            ));
        });

        $view->with(
            'color',
            $this->parseHsl($view->emailList->website_primary_color ?: 'hsl(0, 0%, 0%)'),
        );
    }

    private function minifyCss(string $css): string
    {
        $replacements = [
            '    ' => '',
            "\n" => '',
            ' {' => '{',
            ', ' => ',',
            ': ' => ':',
        ];

        return str_replace(
            search: array_keys($replacements),
            replace: array_values($replacements),
            subject: $css,
        );
    }

    private function parseHsl(string $hsl): array
    {
        $color = [];

        preg_match(
            pattern: '/hsl\s*\(\s*([0-9]+)\s*,\s*([0-9]+%)\s*,\s*([0-9]+%)\s*\)/',
            subject: trim($hsl),
            matches: $color,
        );

        return [
            'hue' => $color[1] ?? '0',
            'saturation' => $color[2] ?? '0%',
            'lightness' => $color[3] ?? '0%',
        ];
    }
}
