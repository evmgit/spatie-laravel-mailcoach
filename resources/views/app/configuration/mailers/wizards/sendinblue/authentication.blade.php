<div>
@include('mailcoach::app.configuration.mailers.wizards.wizardNavigation')
<x-mailcoach::card>
    <x-mailcoach::help>
        <p>
        To be able to send mails through Sendinblue, we should authenticate at Sendinblue.
        </p>
            <p>
            You should first <a href="https://sendinblue.com" target="_blank">create an account</a> at Sendinblue.
            </p>
                <p>
            Next, <a target="_blank" href="https://account.sendinblue.com/advanced/api">create an API key at Sendinblue</a>.
            </p>
    </x-mailcoach::help>

    <form class="form-grid" wire:submit.prevent="submit">
        <x-mailcoach::text-field
            wire:model.defer="apiKey"
            :label="__mc('API Key')"
            name="apiKey"
            type="text"
            autocomplete="off"
        />

        <x-mailcoach::form-buttons>
            <x-mailcoach::button :label="__mc('Verify')"/>
        </x-mailcoach::form-buttons>
    </form>
</x-mailcoach::card>
</div>
