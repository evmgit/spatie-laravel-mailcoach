<div class="card-grid">
    @include('mailcoach::app.configuration.mailers.wizards.wizardNavigation')

    <x-mailcoach::success>
        <p>
            Your Postmark account has been set up. We highly recommend sending a small test campaign to yourself to check if
            everything is working as expected.
        </p>
    </x-mailcoach::success>

    <x-mailcoach::fieldset card :legend="__mc('Summary')">
        <dl class="dl">
            <dt>Open tracking enabled:</dt>
            <dd>
                @if ($mailer->get('open_tracking_enabled'))
                    <x-mailcoach::rounded-icon type="success" icon="fas fa-check" />
                @else
                    <x-mailcoach::rounded-icon type="error" icon="fas fa-times" />
                @endif
            </dd>

            <dt>Click tracking enabled:</dt>
            <dd>
                @if ($mailer->get('click_tracking_enabled'))
                    <x-mailcoach::rounded-icon type="success" icon="fas fa-check" />
                @else
                    <x-mailcoach::rounded-icon type="error" icon="fas fa-times" />
                @endif
            </dd>

            <dt>Message stream ID:</dt>
            <dd>
                {{ $mailer->get('streamId') }}
            </dd>

            <dt>Signing secret:</dt>
            <dd>
                {{ $mailer->get('signing_secret') }}
            </dd>

            <dt>Throttling</dt>
            <dd>
                <p><strong>{{ $mailer->get('mails_per_timespan') }}</strong> {{ __mc('mails every') }} <strong>{{ $mailer->get('timespan_in_seconds') }}</strong> {{ __mc_choice('second|seconds', $mailer->get('timespan_in_seconds')) }}</p>
            </dd>
        </dl>
    </x-mailcoach::fieldset>

    @include('mailcoach::app.configuration.mailers.partials.mailerName')

    <x-mailcoach::card buttons>
        <x-mailcoach::button :label="__mc('Send test email')" x-on:click.prevent="$store.modals.open('send-test')" />
    </x-mailcoach::card>
</div>
