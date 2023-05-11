<div>
    @include('mailcoach::app.configuration.mailers.wizards.wizardNavigation')

    <x-mailcoach::card>

        <x-mailcoach::help>
            <p>
            To be able to send mails through SMTP, we should first authenticate at the SMTP server.
            </p>
            <p>
            Be aware that if you use SMTP to send mails, we won't be able to track opens, clicks, bounces and complaints.
            </p>
        </x-mailcoach::help>

        <form class="form-grid" wire:submit.prevent="submit">
            <x-mailcoach::text-field
                wire:model.defer="host"
                :label="__mc('Host')"
                name="host"
                type="text"
                autocomplete="off"
            />

            <x-mailcoach::text-field
                wire:model.defer="port"
                :label="__mc('Port')"
                name="port"
                type="number"
                autocomplete="off"
            />

            <x-mailcoach::text-field
                wire:model.defer="username"
                :label="__mc('Username')"
                name="username"
                type="text"
                autocomplete="off"
            />

            <x-mailcoach::text-field
                wire:model.defer="password"
                :label="__mc('Password')"
                name="password"
                type="password"
                autocomplete="off"
            />

            <x-mailcoach::select-field
                wire:model.defer="encryption"
                :label="__mc('Encryption')"
                name="encryption"
                :options="$encryptionOptions"
            />

            <x-mailcoach::form-buttons>
                <x-mailcoach::button :label="__mc('Verify')"/>
            </x-mailcoach::form-buttons>
        </form>

    </x-mailcoach::card>
</div>
