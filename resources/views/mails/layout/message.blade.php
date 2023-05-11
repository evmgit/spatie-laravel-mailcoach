@component('mail::layout')
{{-- Header --}}
@slot('header')
@component('mail::header', ['url' => config('app.url')])
<img width="570" style="max-width:570px;width:100%" src="{{ asset('/emails/header.png') }}" alt="Mailcoach">
@endcomponent
@endslot

{{-- Body --}}
{{ $slot }}

{{-- Subcopy --}}
@isset($subcopy)
@slot('subcopy')
@component('mail::subcopy')
{{ $subcopy }}
@endcomponent
@endslot
@endisset

{{-- Footer --}}
@slot('footer')
@component('mail::footer')
<a href="{{config('app.url')}}">Powered by {{ config('app.name') }}</a>
@endcomponent
@endslot
@endcomponent
