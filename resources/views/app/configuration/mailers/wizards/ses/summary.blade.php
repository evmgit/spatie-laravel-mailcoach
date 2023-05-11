<div class="card-grid">
    @include('mailcoach::app.configuration.mailers.wizards.wizardNavigation')

    <x-mailcoach::success>
        <p>
            Your SES account has been set up. We highly recommend sending a small test campaign to yourself to check if
            everything is working as expected.
        </p>
    </x-mailcoach::success>

    @if($isInSandboxMode)
        <x-mailcoach::warning>
            <p>
                Your SES account is currently in <a href="https://docs.aws.amazon.com/ses/latest/dg/request-production-access.html" class="link" target="_blank">sandbox mode</a>. This means that you can only send to emails that are verified with Amazon.
            </p>
        </x-mailcoach::warning>
    @elseif($mailer->get('timespan_in_seconds') === 1 && $mailer->get('mails_per_timespan') === 1)
        <x-mailcoach::warning>
            Your account is not in sandbox mode but your throttling settings are set to 1 mail / second. You can find your sending limit in your SES Account Dashboard to update the throttling config for faster campaigns.
        </x-mailcoach::warning>
    @endif

    <x-mailcoach::fieldset card :legend="__mc('Summary')">
        <dl class="dl">
            <dt>Access Key:</dt>
            <dd>
                {{ $mailer->get('ses_key') }}
            </dd>

            <dt>Region:</dt>
            <dd>
                {{ $mailer->get('ses_region') }}
            </dd>

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

            <dt>Configuration set name:</dt>
            <dd>
                {{ $mailer->get('ses_configuration_set') }}
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
