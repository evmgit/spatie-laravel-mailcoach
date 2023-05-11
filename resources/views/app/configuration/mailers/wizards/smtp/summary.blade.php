<div class="card-grid">
    @include('mailcoach::app.configuration.mailers.wizards.wizardNavigation')

    <x-mailcoach::success>
        <p>
            Your SMTP mailer has been set up. We highly recommend sending a small test campaign to yourself to check if
            everything is working as expected.
        </p>
    </x-mailcoach::success>

    <x-mailcoach::fieldset card :legend="__mc('Summary')">
        <dl class="dl">
            <dt>Host</dt>
            <dd>
                {{ $mailer->get('host') }}
            </dd>

            <dt>Port</dt>
            <dd>
                {{ $mailer->get('port') }}
            </dd>

            <dt>Username</dt>
            <dd>
                {{ $mailer->get('username') }}
            </dd>

            <dt>Encryption</dt>
            <dd>
                {{ $mailer->get('encryption') === '' ? 'None' : $mailer->get('encryption') }}
            </dd>

            <dt>Throttling</dt>
            <dd>
                <p><strong>{{ $mailer->get('mails_per_timespan') }}</strong> {{ __mc('mails every') }} <strong>{{ $mailer->get('timespan_in_seconds') }}</strong> {{ __mc_choice('second|seconds', $mailer->get('timespan_in_seconds', 1)) }}</p>
            </dd>
        </dl>
    </x-mailcoach::fieldset>

    @include('mailcoach::app.configuration.mailers.partials.mailerName')

    <x-mailcoach::card buttons>
    <x-mailcoach::button class="mt-4" :label="__mc('Send test email')" x-on:click.prevent="$store.modals.open('send-test')" />
    </x-mailcoach::card>
</div>
