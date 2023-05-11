<form
    class="card-grid"
    wire:submit.prevent="save"
    @keydown.prevent.window.cmd.s="$wire.call('save')"
    @keydown.prevent.window.ctrl.s="$wire.call('save')"
    method="POST"
    data-dirty-check
>
<x-mailcoach::card>
    <x-mailcoach::text-field :label="__mc('Name')" name="mail.name" wire:model.lazy="mail.name" required  />

    <x-mailcoach::text-field :label="__mc('Subject')" name="mail.subject" wire:model.lazy="mail.subject"  />
</x-mailcoach::card>

    <x-mailcoach::fieldset card :legend="__mc('Sender')">
        <x-mailcoach::info class="-mt-4">{!! __mc('Leave empty to use the defaults from the automation\'s email list. These will also be set the first time the automation mail is sent.') !!}</x-mailcoach::info>
        <div class="grid grid-cols-2 gap-6">
            <x-mailcoach::text-field :label="__mc('From email')" name="mail.from_email" wire:model.lazy="mail.from_email"
                                     type="email" />

            <x-mailcoach::text-field :label="__mc('From name')" name="mail.from_name" wire:model.lazy="mail.from_name" />

            <x-mailcoach::text-field :label="__mc('Reply-to email')" name="mail.reply_to_email" wire:model.lazy="mail.reply_to_email"
                                     type="email"/>

            <x-mailcoach::text-field :label="__mc('Reply-to name')" name="mail.reply_to_name" wire:model.lazy="mail.reply_to_name"/>
        </div>
    </x-mailcoach::fieldset>

    <x-mailcoach::fieldset card :legend="__mc('Tracking')">
        <div class="form-field">
            <x-mailcoach::info>
                {!! __mc('Open & Click tracking are managed by your email provider.') !!}
            </x-mailcoach::info>
        </div>

        <div class="form-field">
            <label class="label">{{ __mc('Subscriber Tags') }}</label>
            <div class="checkbox-group">
                <x-mailcoach::checkbox-field :label="__mc('Add tags to subscribers for opens & clicks')" name="mail.add_subscriber_tags" wire:model="mail.add_subscriber_tags" />
                <x-mailcoach::checkbox-field :label="__mc('Add individual link tags')" name="mail.add_subscriber_link_tags" wire:model="mail.add_subscriber_link_tags" />
            </div>
        </div>

        <x-mailcoach::help>
            <p class="text-sm mb-2">{{ __mc('When checked, the following tags will automatically get added to subscribers that open or click the automation mail:') }}</p>
            <p>
                <span class="tag-neutral">{{ "automation-mail-{$mail->uuid}-opened" }}</span>
                <span class="tag-neutral">{{ "automation-mail-{$mail->uuid}-clicked" }}</span>
            </p>
            <p class="text-sm mt-2">{{ __mc('When "Add individual link tags" is checked, it will also add a unique tag per link') }}</p>
        </x-mailcoach::help>

        <div class="form-field">
            <label class="label">{{ __mc('UTM Tags') }}</label>
            <div class="checkbox-group">
                <x-mailcoach::checkbox-field :label="__mc('Automatically add UTM tags')" name="mail.utm_tags" wire:model="mail.utm_tags" />
            </div>
        </div>

        <x-mailcoach::help>
            <p class="text-sm mb-2">{{ __mc('When checked, the following UTM Tags will automatically get added to any links in your campaign:') }}</p>
            <dl class="markup-dl">
                <dt><strong>utm_source</strong></dt><dd>newsletter</dd>
                <dt><strong>utm_medium</strong></dt><dd>email</dd>
                <dt><strong>utm_campaign</strong></dt><dd>{{ \Illuminate\Support\Str::slug($mail->name) }}</dd>
            </dl>
        </x-mailcoach::help>
    </x-mailcoach::fieldset>

    <x-mailcoach::fieldset card :legend="__mc('Usage in Mailcoach API')">
        <div>
            <x-mailcoach::help>
                {!! __mc('Whenever you need to specify a <code>:resourceName</code> in the Mailcoach API and want to use this :resource, you\'ll need to pass this value', [
                'resourceName' => 'automationMail uuid',
                'resource' => 'automation email',
            ]) !!}
                <p class="mt-4">
                    <x-mailcoach::code-copy class="flex items-center justify-between max-w-md" :code="$mail->uuid"></x-mailcoach::code-copy>
                </p>
            </x-mailcoach::help>
        </div>
    </x-mailcoach::fieldset>

    <x-mailcoach::card  buttons>
        <x-mailcoach::button :label="__mc('Save settings')" />
    </x-mailcoach::card>
</form>
