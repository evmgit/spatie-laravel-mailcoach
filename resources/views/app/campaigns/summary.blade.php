<?php
    /** @var \Spatie\Mailcoach\Domain\Campaign\Models\Campaign $campaign */
?>
<div class="card-grid" id="campaign-summary" wire:poll.5s.keep-alive>
    <x-mailcoach::card>
        @if ($campaign->isPreparing())
            <x-mailcoach::help sync full>
                <div class="flex justify-between items-center w-full">
                    <div>
                        {{ __mc('Campaign') }}
                        <strong>{{ $campaign->name }}</strong>
                        {{ __mc('is preparing to send to') }}

                        @if($campaign->emailList)
                            <a href="{{ route('mailcoach.emailLists.subscribers', $campaign->emailList) }}">{{ $campaign->emailList->name }}</a>
                        @else
                            &lt;{{ __mc('deleted list') }}&gt;
                        @endif

                        @if($campaign->usesSegment())
                            ({{ $campaign->segment_description }})
                        @endif
                        ...
                    </div>

                    <x-mailcoach::confirm-button
                        class="ml-auto text-red-500 underline"
                        onConfirm="() => $wire.cancelSending()"
                        :confirm-text="__mc('Are you sure you want to cancel sending this campaign?')">
                        Cancel
                    </x-mailcoach::confirm-button>
                </div>
            </x-mailcoach::help>
            <div class="progress-bar">
                <div class="progress-bar-value" style="width:0"></div>
            </div>
        @endif
        @if ($campaign->isCancelled())
            <x-mailcoach::error full>
                <div class="flex justify-between items-center w-full">
                    <p>
                        <span class="inline-block">{{ __mc('Campaign') }}</span>
                        <strong>{{ $campaign->name }}</strong>

                        {{ __mc('sending is cancelled.') }}

                        {{ __mc('It was sent to :sendsCount/:sentToNumberOfSubscribers :subscriber of', [
                            'sendsCount' => number_format($campaign->sendsCount()),
                            'sentToNumberOfSubscribers' => number_format($campaign->sent_to_number_of_subscribers),
                            'subscriber' => __mc_choice('subscriber|subscribers', $campaign->sent_to_number_of_subscribers)
                        ]) }}

                        @if($campaign->emailList)
                            <a href="{{ route('mailcoach.emailLists.subscribers', $campaign->emailList) }}">{{ $campaign->emailList->name }}</a>
                        @else
                            &lt;{{ __mc('deleted list') }}&gt;
                        @endif

                        @if($campaign->usesSegment())
                            ({{ $campaign->segment_description }})
                        @endif
                    </p>
                </div>
            </x-mailcoach::error>
            @if($campaign->sent_to_number_of_subscribers)
                <div class="progress-bar">
                    <div class="progress-bar-value" style="width:{{ ($campaign->sendsCount() / $campaign->sent_to_number_of_subscribers) * 100 }}%"></div>
                </div>
            @endif
        @endif
        @if($campaign->isSending() && $campaign->sent_to_number_of_subscribers)
            @php($total = $campaign->sent_to_number_of_subscribers * 2)
            <x-mailcoach::help sync full>
                <div class="flex justify-between items-center w-full">
                    <span class="block">
                        <span class="inline-block">{{ __mc('Campaign') }}</span>
                        <strong>{{ $campaign->name }}</strong>

                        @if ($campaign->sendsCount() === $campaign->sent_to_number_of_subscribers)
                            {{ __mc('is finishing up') }}
                        @else
                            {{ __mc('is sending to :sentToNumberOfSubscribers :subscriber of', [
                                'sentToNumberOfSubscribers' => number_format($campaign->sent_to_number_of_subscribers),
                                'subscriber' => __mc_choice('subscriber|subscribers', $campaign->sent_to_number_of_subscribers)
                            ]) }}

                            @if($campaign->emailList)
                                <a href="{{ route('mailcoach.emailLists.subscribers', $campaign->emailList) }}">{{ $campaign->emailList->name }}</a>
                            @else
                                &lt;{{ __mc('deleted list') }}&gt;
                            @endif
                            @if($campaign->usesSegment())
                                ({{ $campaign->segment_description }})
                            @endif
                        @endif
                    </span>

                    <x-mailcoach::confirm-button
                        class="ml-auto text-red-500 underline"
                        onConfirm="() => $wire.cancelSending()"
                        :confirm-text="__mc('Are you sure you want to cancel sending this campaign?')">
                        Cancel
                    </x-mailcoach::confirm-button>
                </div>
            </x-mailcoach::help>
            <div class="progress-bar">
                <div class="progress-bar-value" style="width:{{ (($campaign->sends()->count() + $campaign->sendsCount()) / $total) * 100 }}%"></div>
            </div>
        @endif
        @if($campaign->isSent())
            @if($pendingCount = $campaign->sends()->pending()->count())
                <x-mailcoach::help sync full>
                    <div class="flex justify-between items-center w-full">
                    <span class="block">
                        <span class="inline-block">{{ __mc('Campaign') }}</span>
                        <strong>{{ $campaign->name }}</strong>

                        {!! __mc('is retrying <strong>:sendsCount :sends</strong> to', [
                            'sendsCount' => number_format($pendingCount),
                            'sends' => __mc_choice('send|sends', $pendingCount)
                        ]) !!}

                        @if($campaign->emailList)
                            <a href="{{ route('mailcoach.emailLists.subscribers', $campaign->emailList) }}">{{ $campaign->emailList->name }}</a>
                        @else
                            &lt;{{ __mc('deleted list') }}&gt;
                        @endif
                        @if($campaign->usesSegment())
                            ({{ $campaign->segment_description }})
                        @endif
                    </span>
                    </div>
                </x-mailcoach::help>
                <div class="progress-bar">
                    <div class="progress-bar-value" style="width:{{ (($campaign->sendsCount() - $pendingCount) / $campaign->sendsCount()) * 100 }}%"></div>
                </div>
            @endif

            <x-mailcoach::success class="md:max-w-full" full>
                @php($sendsCount = $campaign->sendsWithoutInvalidated()->count())
                @php($successfulSendsCount = $sendsCount - ($failedSendsCount ?? 0) - ($pendingCount ?? 0))
                <div>
                    {{ __mc('Campaign') }}
                    <a target="_blank" href="{{ $campaign->webviewUrl() }}"><strong>{{ $campaign->name }}</strong></a>
                    {{ __mc('was delivered successfully to') }}
                    <strong>{{ number_format($successfulSendsCount) }} {{ __mc_choice('subscriber|subscribers', $successfulSendsCount) }}</strong>

                    {{ __mc('of') }}

                    @if($campaign->emailList)
                        <a href="{{ route('mailcoach.emailLists.subscribers', $campaign->emailList) }}">{{ $campaign->emailList->name }}</a>
                    @else
                        &lt;{{ __mc('deleted list') }}&gt;
                    @endif
                    @if($campaign->usesSegment())
                        ({{ $campaign->segment_description }})
                    @endif
                </div>

                @if($failedSendsCount)
                    <div>
                        <i class="fas fa-times text-red-500"></i>
                    </div>
                    <div>
                        {{ __mc('Delivery failed for') }} <strong>{{ number_format($failedSendsCount) }}</strong> {{ __mc_choice('subscriber|subscribers', $failedSendsCount) }}.
                        <a class="underline" href="{{ route('mailcoach.campaigns.outbox', $campaign) . '?filter[type]=failed' }}">{{ __mc('Check the outbox') }}</a>.
                    </div>
                @endif

                <div class="text-sm">{{ $campaign->sent_at->toMailcoachFormat() }}</div>
            </x-mailcoach::success>
        @endif

        @if ($campaign->opens()->count() || $campaign->clicks()->count())
            <livewire:mailcoach::campaign-statistics :campaign="$campaign" />
        @endif
    </x-mailcoach::card>

    <x-mailcoach::card>
        <h2 class="markup-h2 mb-0">
            {{ __mc('Totals') }}
        </h2>
        @include('mailcoach::app.campaigns.partials.statistics')
    </x-mailcoach::card>
</div>
