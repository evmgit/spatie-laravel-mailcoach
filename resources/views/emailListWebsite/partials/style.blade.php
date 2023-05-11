<?php /** @var \Spatie\Mailcoach\Domain\Audience\Models\EmailList $emailList */ ?>

<style>
    {{ $css() }}

    :root {
        --accent-hue: {{ $color['hue'] }};
        --accent-saturation: {{ $color['saturation'] }};
        --accent-lightness: {{ $color['lightness'] }};
    }
</style>

@includeFirst([
    'mailcoach::emailListWebsite.themes.' . $emailList->website_theme,
    'mailcoach::emailListWebsite.themes.default',
])
