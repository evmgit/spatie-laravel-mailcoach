<?php

function __mc(string $key, array $replace = [], ?string $locale = null): string
{
    $result = __('mailcoach::mailcoach.'.$key, $replace, $locale);

    return str_replace('mailcoach::mailcoach.', '', $result);
}

function __mc_choice(string $key, int $number, array $replace = [], ?string $locale = null): string
{
    $result = trans_choice('mailcoach::mailcoach.'.$key, $number, $replace, $locale);

    return str_replace('mailcoach::mailcoach.', '', $result);
}
