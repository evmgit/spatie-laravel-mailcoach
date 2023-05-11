<div>

@include('mailcoach::app.configuration.mailers.wizards.wizardNavigation')
<x-mailcoach::card>
    <x-mailcoach::help>
        <p>
        To be able to send mails through Amazon SES, we should first authenticate at Amazon.
        </p>
            <p>
            You should first <a href="https://aws.amazon.com" target="_blank">create an account</a> at AWS.
            </p>
                <p>
            Next, <a target="_blank" href="https://mailcoach.app/docs/self-hosted/v6/using-mailcoach/configuring-mail-providers/amazon-ses#content-key-and-secret-sending-emails">create an Access Key ID and Secret Access Key</a>, and fill them in the form below.
            </p>
        <p>Mailcoach needs the <strong>AmazonSESFullAccess</strong> and <strong>AmazonSNSFullAccess</strong> permissions to set up.</p>
    </x-mailcoach::help>

        <form class="form-grid" wire:submit.prevent="submit">
            <x-mailcoach::text-field
                wire:model.defer="key"
                :label="__mc('Key')"
                name="key"
                type="text"
                autocomplete="off"
            />

            <x-mailcoach::text-field
                wire:model.defer="secret"
                :label="__mc('Secret')"
                name="secret"
                type="password"
                autocomplete="off"
            />

            <x-mailcoach::select-field
                wire:model.defer="region"
                :label="__mc('Region')"
                name="region"
                :options="$regions"
                placeholder="Choose a region"
            />

            <x-mailcoach::form-buttons>
                <x-mailcoach::button :label="__mc('Verify')"/>
            </x-mailcoach::form-buttons>
        </form>
</x-mailcoach::card>
</div>
