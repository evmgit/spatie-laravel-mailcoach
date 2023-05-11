<x-mailcoach::card wire:init="checkLinks">
    @if ($campaign->isEditable())
        <div class="grid gap-2">
            @if($campaign->isReady())
                @if (! $campaign->htmlContainsUnsubscribeUrlPlaceHolder() || $campaign->sizeInKb() > 102)
                    <x-mailcoach::warning>
                        {!! __mc('Campaign <strong>:campaign</strong> can be sent, but you might want to check your content.', ['campaign' => $campaign->name]) !!}

                        @if($campaign->scheduled_at)
                            <div class="mt-4 flex gap-2 items-center">
                                <x-mailcoach::rounded-icon type="warning" icon="far fa-clock"/>
                                <span class="font-semibold">
                                    {{ __mc('Scheduled for delivery at :scheduledAt', ['scheduledAt' => $campaign->scheduled_at->toMailcoachFormat()]) }}.
                                </span>
                            </div>
                        @endif
                    </x-mailcoach::warning>
                @else
                    <x-mailcoach::success>
                        {!! __mc('Campaign <strong>:campaign</strong> is ready to be sent.', ['campaign' => $campaign->name]) !!}

                        @if($campaign->scheduled_at)
                            <div class="mt-4 flex gap-2 items-center">
                                <x-mailcoach::rounded-icon type="success" icon="far fa-clock"/>
                                <span class="font-semibold">
                                    {{ __mc('Scheduled for delivery at :scheduledAt', ['scheduledAt' => $campaign->scheduled_at->toMailcoachFormat()]) }}.
                                </span>
                            </div>
                        @endif
                    </x-mailcoach::success>
                @endif
            @else
                <x-mailcoach::error>
                    {{ __mc('You need to check some settings before you can deliver this campaign.') }}
                </x-mailcoach::error>
            @endif
        </div>
    @endif

    <dl
        class="mt-8 dl"
    >
        @if ($campaign->emailList)
            <dt>
                <x-mailcoach::health-label reverse :test="true" :label="__mc('From')"/>
            </dt>

            <dd>
                <span>
                    {{ $campaign->emailList->default_from_email }} {{ $campaign->emailList->default_from_name ? "({$campaign->emailList->default_from_name})" : '' }}
                    <a href="{{ route('mailcoach.emailLists.general-settings', $campaign->emailList) }}" class="link">{{ strtolower(__mc('Edit')) }}</a>
                </span>
            </dd>

            @if ($campaign->emailList->default_reply_to_email)
                <dt>
                    <x-mailcoach::health-label reverse :test="true" :label="__mc('Reply-to')"/>
                </dt>

                <dd>
                    <span>
                        {{ $campaign->emailList->default_reply_to_email }} {{ $campaign->emailList->default_reply_to_name ? "({$campaign->emailList->default_reply_to_name})" : '' }}
                        <a href="{{ route('mailcoach.emailLists.general-settings', $campaign->emailList) }}" class="link">{{ strtolower(__mc('Edit')) }}</a>
                    </span>
                </dd>
            @endif

            <dt>
                <x-mailcoach::health-label reverse :test="$campaign->segmentSubscriberCount()" :label="__mc('To')"/>
            </dt>

            <dd>
                <div>
                    @if($campaign->emailListSubscriberCount())
                        {{ $campaign->emailList->name }}
                        @if($campaign->usesSegment())
                            ({{ $campaign->getSegment()->description() }})
                        @endif
                        <span class="ml-2 tag-neutral text-xs">
                            {{ $campaign->segmentSubscriberCount() }}
                            <span class="ml-1 font-normal">
                                {{ __mc_choice('subscriber|subscribers', $campaign->segmentSubscriberCount()) }}
                            </span>
                        </span>
                    @elseif($campaign->emailList)
                        {{ __mc('Selected list has no subscribers') }}
                    @else
                        {{ __mc('No list selected') }}
                    @endif
                    <a href="{{ route('mailcoach.campaigns.settings', $campaign) }}" class="link">{{ strtolower(__mc('Edit')) }}</a>
                </div>
            </dd>
        @endif

        <dt>
            <x-mailcoach::health-label reverse :test="$campaign->subject" :label="__mc('Subject')"/>
        </dt>

        <dd>
            <span>
                {{ $campaign->subject ?? __mc('Subject is empty') }}
                <a href="{{ route('mailcoach.campaigns.settings', $campaign) }}" class="link">{{ strtolower(__mc('Edit')) }}</a>
            </span>
        </dd>

        @if ($campaign->emailList)
            <dt>
                <x-mailcoach::health-label reverse warning :test="$campaign->getMailerKey() && $campaign->getMailerKey() !== 'log'" :label="__mc('Mailer')"/>
            </dt>
            <dd>
                <div>
                    {{ $campaign->getMailer()?->name ?? $campaign->emailList->campaign_mailer }} <a href="{{ route('mailcoach.emailLists.mailers', $campaign->emailList) }}" class="link">{{ strtolower(__mc('Edit')) }}</a>
                </div>
            </dd>
        @endif

        <dt>
            @if($campaign->html && $campaign->hasValidHtml())
                <x-mailcoach::health-label reverse
                    :test="$campaign->htmlContainsUnsubscribeUrlPlaceHolder() && $campaign->sizeInKb() < 102"
                    warning="true"
                    :label="__mc('Content')"/>
            @else
                <x-mailcoach::health-label reverse :test="false" :label="__mc('Content')"/>
            @endif
        </dt>


        <dd class="grid gap-4 max-w-full overflow-scroll">
            @if($campaign->html && $campaign->hasValidHtml())
                @if ($campaign->htmlContainsUnsubscribeUrlPlaceHolder() && $campaign->sizeInKb() < 102)
                    <p class="markup-code">
                        {{ __mc('No problems detected!') }}
                    </p>
                @else
                    @if (! $campaign->htmlContainsUnsubscribeUrlPlaceHolder())
                        <p class="markup-code">
                            {{ __mc("Without a way to unsubscribe, there's a high chance that your subscribers will complain.") }}
                            {!! __mc('Consider adding the <code>::unsubscribeUrl::</code> placeholder.') !!}
                        </p>
                    @endif
                    @if ($campaign->sizeInKb() >= 102)
                        <p class="markup-code">
                            {{ __mc("Your email's content size is larger than 102kb (:size). This could cause Gmail to clip your campaign.", ['size' => "{$campaign->sizeInKb()}kb"]) }}
                        </p>
                    @endif
                @endif
            @else
                @if(empty($campaign->html))
                    {{ __mc('Content is missing') }}
                @else
                    <p>{{ __mc('HTML is invalid') }}</p>
                    <p>{!! $campaign->htmlError() !!}</p>
                @endif
            @endif

            @if($campaign->html && $campaign->hasValidHtml())
                <div>
                    <x-mailcoach::button-secondary x-on:click="$store.modals.open('preview')" :label="__mc('Preview')"/>
                    @if ($campaign->getMailerKey())
                        <x-mailcoach::button-secondary x-on:click="$store.modals.open('send-test')" :label="__mc('Send Test')"/>
                    @endif
                </div>

                <x-mailcoach::preview-modal :title="__mc('Preview') . ' - ' . $campaign->subject" :html="$campaign->html" />

                <x-mailcoach::modal :title="__mc('Send Test')" name="send-test" :dismissable="true">
                    <livewire:mailcoach::send-test :model="$campaign" />
                </x-mailcoach::modal>
            @endif
        </dd>

        <dt>
            <span class="inline-flex gap-2 items-center md:flex-row-reverse">
                <x-mailcoach::rounded-icon type="neutral" icon="fas fa-link"/>
                <span>
                    {{ __mc('Links') }}
                </span>
            </span>
        </dt>

        <dd>
            @php($tags = [])
            @if (count($links))
                <p class="markup-code">
                    {{ __mc("The following links were found in your campaign, make sure they are valid.") }}
                </p>
                <ul class="grid gap-2">
                    @foreach ($links as $url => $status)
                        <li class="flex items-center gap-x-1">
                            <a target="_blank" class="link" href="{{ $url }}">{{ $url }}</a>
                            @if (!is_null($status))
                                <x-mailcoach::health-label reverse warning :test="$status" />
                            @endif
                            @php($tags[] = \Spatie\Mailcoach\Domain\Shared\Support\LinkHasher::hash($campaign, $url))
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="markup-code">
                    {{ __mc("No links were found in your campaign.") }}
                </p>
            @endif
        </dd>

        @php([$openTracking, $clickTracking] = $campaign->tracking())
        @if ($openTracking || $clickTracking || (is_null($openTracking) && is_null($clickTracking)))
            @if ($campaign->add_subscriber_tags || $campaign->add_subscriber_link_tags)
            <dt>
                <span class="inline-flex gap-2 items-center md:flex-row-reverse">
                    <x-mailcoach::rounded-icon type="neutral" icon="fas fa-tag"/>
                    <span>
                        {{ __mc('Tags') }}
                    </span>
                </span>
            </dt>

            <dd>
                <p class="markup-code">
                    {{ __mc("The following tags will be added to subscribers when they open or click the campaign:") }}
                </p>
                @if (is_null($openTracking) && is_null($clickTracking))
                    <p class="markup-code">
                        {!! __mc('Open & Click tracking are managed by your email provider, this campaign uses the <strong>:mailer</strong> mailer.', ['mailer' => $campaign->getMailerKey()]) !!}
                    </p>
                @endif
                <ul class="flex flex-wrap space-x-2">
                    @if ($campaign->add_subscriber_tags)
                        <li class="tag-neutral">{{ "campaign-{$campaign->uuid}-opened" }}</li>
                        <li class="tag-neutral">{{ "campaign-{$campaign->uuid}-clicked" }}</li>
                    @endif
                    @if ($campaign->add_subscriber_link_tags)
                        @foreach ($tags as $tag)
                            <li class="tag-neutral">{{ $tag }}</li>
                        @endforeach
                    @endif
                </ul>
            </dd>
            @endif
        @endif

        @if (count($validateErrors = $campaign->validateRequirements()))
            <dt>
                <x-mailcoach::health-label reverse :test="false" :label="__mc('Requirements')"/>
            </dt>
            <dd>
                <div class="grid grid-cols-1 gap-y-2 markup-code">
                    @foreach ($validateErrors as $error)
                        <div>{!! $error !!}</div>
                    @endforeach
                </div>
            </dd>
        @endif

        @if ($campaign->isReady())
            <dt>
                <span class="inline-flex gap-2 items-center md:flex-row-reverse">
                    <x-mailcoach::rounded-icon :type="$campaign->scheduled_at ? 'warning' : 'neutral'"
                                               icon="far fa-clock"/>
                    <span>
                        {{ __mc('Timing') }}
                    </span>
                </span>
            </dt>

            <dd x-init="schedule = '{{ $campaign->scheduled_at || $errors->first('scheduled_at') ? 'future' : 'now' }}'" x-data="{ schedule: '' }" x-cloak>
                @if($campaign->scheduled_at)
                    <div>
                        <p class="mb-3">
                            {{ __mc('This campaign is scheduled to be sent at') }}

                            <strong>{{ $campaign->scheduled_at->toMailcoachFormat() }}</strong>.
                        </p>
                        <button type="submit" wire:click.prevent="unschedule" class="button-secondary">
                            {{ __mc('Unschedule') }}
                        </button>
                    </div>
                @elseif ($campaign->isEditable())
                    <div class="radio-group">
                        <x-mailcoach::radio-field
                            name="schedule"
                            option-value="now"
                            :label="__mc('Send immediately')"
                            x-model="schedule"
                        />
                        <x-mailcoach::radio-field
                            name="schedule"
                            option-value="future"
                            :label="__mc('Schedule for delivery in the future')"
                            x-model="schedule"
                        />
                    </div>

                    <form
                        method="POST"
                        wire:submit.prevent="schedule"
                        x-show="schedule === 'future'"
                    >
                        @csrf
                        <div class="flex items-end">
                            <x-mailcoach::date-time-field
                                name="scheduled_at"
                                :value="$scheduled_at_date"
                                required
                            />

                            <button type="submit" class="ml-6 button">
                                {{ __mc('Schedule delivery') }}
                            </button>
                        </div>
                        <p class="mt-2 text-xs text-gray-400">
                            {{ __mc('All times in :timezone', ['timezone' => config('mailcoach.timezone') ?? config('app.timezone')]) }}
                        </p>
                    </form>
                @elseif (! $campaign->sent_to_number_of_subscribers)
                    <div class="flex alert alert-info">
                        <div class="mr-2">
                            <i class="fas fa-sync fa-spin text-blue-500"></i>
                        </div>
                        <div>
                            {{ __mc('Campaign') }}
                            <a target="_blank" href="{{ $campaign->webviewUrl() }}">{{ $campaign->name }}</a>

                            {{ __mc('is preparing to send to') }}

                            @if($campaign->emailList)
                                <a href="{{ route('mailcoach.emailLists.subscribers', $campaign->emailList) }}">{{ $campaign->emailList->name }}</a>
                            @else
                                &lt;{{ __mc('deleted list') }}&gt;
                            @endif
                        </div>
                    </div>
                @elseif ($campaign->isCancelled())
                    <div class="flex alert alert-info">
                        <div class="mr-2">
                            <i class="fas fa-ban text-red-500"></i>
                        </div>
                        <div class="flex justify-between items-center w-full">
                            <p>
                                <span class="inline-block">{{ __mc('Campaign') }}</span>
                                <a class="inline-block" target="_blank"
                                   href="{{ $campaign->webviewUrl() }}">{{ $campaign->name }}</a>

                                {{ __mc('sending is cancelled.', [
                                    'sendsCount' => $campaign->sendsCount(),
                                    'sentToNumberOfSubscribers' => $campaign->sent_to_number_of_subscribers,
                                    'subscriber' => __mc_choice('subscriber|subscribers', $campaign->sent_to_number_of_subscribers)
                                ]) }}

                                {{ __mc('It was sent to :sendsCount/:sentToNumberOfSubscribers :subscriber of', [
                                    'sendsCount' => $campaign->sendsCount(),
                                    'sentToNumberOfSubscribers' => $campaign->sent_to_number_of_subscribers,
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
                    </div>
                @else
                    <div class="flex alert alert-info">
                        <div class="mr-2">
                            <i class="fas fa-sync fa-spin text-blue-500"></i>
                        </div>
                        <div class="flex justify-between items-center w-full">
                            <p>
                                <span class="inline-block">{{ __mc('Campaign') }}</span>
                                <a class="inline-block" target="_blank"
                                   href="{{ $campaign->webviewUrl() }}">{{ $campaign->name }}</a>

                                {{ __mc('is sending to :sendsCount/:sentToNumberOfSubscribers :subscriber of', [
                                    'sendsCount' => $campaign->sendsCount(),
                                    'sentToNumberOfSubscribers' => $campaign->sent_to_number_of_subscribers,
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
                    </div>
                @endif

                @if ($campaign->isEditable())
                    <div
                        x-show="schedule === 'now'"
                    >
                        <x-mailcoach::button x-on:click="$store.modals.open('send-campaign')" :label="__mc('Send now')"/>
                    </div>
                    <x-mailcoach::modal name="send-campaign" :dismissable="true" x-data>
                        <div class="grid gap-8 p-6">
                            <p class="text-lg">
                                {{ __mc('Are you sure you want to send this campaign to') }}
                                <strong class="font-semibold">
                                    {{ number_format($campaign->segmentSubscriberCount()) }}
                                    {{ $campaign->segmentSubscriberCount() === 1 ? __mc('subscriber') : __mc('subscribers') }}
                                </strong>?
                            </p>

                            <x-mailcoach::button
                                x-on:click.prevent="Livewire.emit('send-campaign')"
                                class="button button-red"
                                :label="__mc('Yes, send now!')"
                            />
                        </div>
                    </x-mailcoach::modal>
                @endif
            </dd>
        @endif
    </dl>
</x-mailcoach::card>
