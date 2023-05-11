<div>
@include('mailcoach::app.configuration.mailers.wizards.wizardNavigation')
<x-mailcoach::card>
    <x-mailcoach::help>
        Sendgrid can be configured to track bounces and complaints. It will send webhooks to Mailcoach, that will be used to
        automatically unsubscribe people.<br/><br/>Optionally, SendGrid can also send webhooks to inform Mailcoach of opens and
        clicks.
    </x-mailcoach::help>

    <form class="form-grid" wire:submit.prevent="configureSendGrid">
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

        <x-mailcoach::form-buttons>
            <x-mailcoach::button :label="__mc('Configure SendGrid')"/>
        </x-mailcoach::form-buttons>
    </form>
</x-mailcoach::card>
</div>
