<div>
@include('mailcoach::app.configuration.mailers.wizards.wizardNavigation')
<x-mailcoach::card>

    <x-mailcoach::help>
        Mailgun can be configured track bounces and complaints. It will send webhooks to Mailcoach, that will be used to
        automatically unsubscribe people.<br/><br/>Optionally, Mailgun can also send webhooks to inform Mailcoach of opens and
        clicks.
    </x-mailcoach::help>

        <form class="form-grid" wire:submit.prevent="configureMailgun">
            <x-mailcoach::checkbox-field
                :label="__mc('Enable open tracking')"
                name="trackOpens"
                wire:model.defer="trackOpens"
            />

            <x-mailcoach::checkbox-field
                :label="__mc('Enable click tracking')"
                name="trackClicks"
                wire:model.defer="trackClicks"
            />

            <x-mailcoach::text-field
                name="signingSecret"
                wire:model.lazy="signingSecret"
                :label="__mc('Webhook signing secret')"
                :help="__mc('You can find it <a class=\'link\' href=\':url\'>in your API Security screen</a>', ['url' => 'https://app.mailgun.com/app/account/security/api_keys'])"
            />

            <x-mailcoach::form-buttons>
                <x-mailcoach::button :label="__mc('Configure Mailgun')"/>
            </x-mailcoach::form-buttons>
        </form>
</x-mailcoach::card>
</div>
