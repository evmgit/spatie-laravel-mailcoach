<div>
@include('mailcoach::app.configuration.mailers.wizards.wizardNavigation')

<x-mailcoach::card>

    <x-mailcoach::help>
        <p>
        To be able to send mails through Mailgun, we should authenticate with Mailgun.
        </p>
            <p>
            You should first <a href="https://mailgun.com" target="_blank">create an account</a> at Mailgun.
            </p>
                <p>
            Next, <a target="_blank" href="https://mailcoach.app/docs/self-hosted/v6/using-mailcoach/configuring-mail-providers/mailgun#content-email-configuration">create and API key at Mailgun</a>. Make sure it has the "Mail Send" and "Tracking" permissions.
            </p>
    </x-mailcoach::help>

        <form class="form-grid" wire:submit.prevent="submit">
            <x-mailcoach::text-field
                wire:model.lazy="apiKey"
                :label="__mc('API Key')"
                :help="__mc('You can find it <a class=\'link\' href=\':url\'>in your API Security screen</a>', ['url' => 'https://app.mailgun.com/app/account/security/api_keys'])"
                name="apiKey"
                type="text"
                autocomplete="off"
            />

            <x-mailcoach::text-field
                wire:model.lazy="domain"
                :label="__mc('Domain')"
                :help="__mc('Your sending domain without http(s)://', ['url' => 'https://app.mailgun.com/app/account/security/api_keys'])"
                name="domain"
                type="text"
                autocomplete="off"
            />

            <x-mailcoach::select-field
                wire:model.lazy="baseUrl"
                :label="__mc('Base URL')"
                :help="__mc('If you have a EU flag in front of your domain, choose api.eu.mailgun.net')"
                name="baseUrl"
                :options="[
                    'api.mailgun.net' => 'api.mailgun.net',
                    'api.eu.mailgun.net' => 'api.eu.mailgun.net',
                ]"
            />

            <x-mailcoach::form-buttons>
                <x-mailcoach::button :label="__mc('Verify')"/>
            </x-mailcoach::form-buttons>
        </form>

</x-mailcoach::card>
</div>
