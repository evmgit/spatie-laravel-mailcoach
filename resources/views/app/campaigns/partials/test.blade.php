<div class="flex items-end">
    <div class="flex-grow max-w-xl">
        <x-mailcoach::text-field
            :label="__mc('Test addresses')"
            :placeholder="__mc('Email(s) comma separated')"
            name="emails"
            :required="true"
            type="text"
            :value="cache()->get('mailcoach-test-email-addresses')"
        />
    </div>
<div wire:click="sendTest">My button</div>
    <x-mailcoach::button type="" wire:click="sendTest" class="ml-2" :label="__mc('Send test')"/>
</div>

