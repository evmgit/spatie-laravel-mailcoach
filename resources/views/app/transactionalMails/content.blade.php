<x-mailcoach::card>
    <dl class="dl">
        <dt>{{ __mc('Subject') }}</dt>
        <dd>{{ $transactionalMail->subject }}</dd>

        <x-mailcoach::address-definition :label="__mc('From')" :addresses="$transactionalMail->from"/>
        <x-mailcoach::address-definition :label="__mc('To')" :addresses="$transactionalMail->to"/>
        <x-mailcoach::address-definition :label="__mc('Cc')" :addresses="$transactionalMail->cc"/>
        <x-mailcoach::address-definition :label="__mc('Bcc')" :addresses="$transactionalMail->bcc"/>

        @if(collect($transactionalMail->attachments)->count() > 0)
            <dt>
                {{ __mc('Attachments') }}
            </dt>
            <dd>
                <ul class="list-disc list-inside">
                    @foreach(collect($transactionalMail->attachments) as $attachment)
                            <li>
                                {{ $attachment }}
                            </li>
                    @endforeach
                </ul>
            </dd>
        @endif

        <dt class="flex items-start">
            <div>{{ __mc('Body') }}</div>
        </dt>
        <dd>
            <x-mailcoach::web-view :html="$transactionalMail->body" />
        </dd>
    </dl>
</x-mailcoach::card>
