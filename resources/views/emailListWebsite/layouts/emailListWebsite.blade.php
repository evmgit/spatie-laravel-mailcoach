<?php /** @var \Spatie\Mailcoach\Domain\Audience\Models\EmailList $emailList */ ?>
<!DOCTYPE html>
<html class="h-full antialiased" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @isset($title)
        <title>{{ $title }} | {{ $emailList->website_title }}</title>
    @else
        <title>{{ $emailList->website_title }}</title>
    @endisset
    <meta name="theme-color" content="{{ $emailList->website_primary_color }}">
    <meta name="description" content="{{ $emailList->website_intro }}">

    @if($favicon = $emailList->getFirstMediaUrl('header', 'favicon'))
        <link rel="icon" href="{{ $favicon }}">
    @endif

    @if($emailList->campaigns_feed_enabled)
        <link rel="alternate" type="application/atom+xml" href="{{ route('mailcoach.feed', $emailList) }}" title="{{ $emailList->website_title }}">
    @endif

    @include('mailcoach::emailListWebsite.partials.style')
</head>
<body>
    <div class="layout">
        @include('mailcoach::emailListWebsite.partials.header')
        {{ $slot }}
        @include('mailcoach::emailListWebsite.partials.footer')
    </div>
</body>
</html>
