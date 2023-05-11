<form action="" class="grid grid-cols-1 gap-6" wire:submit.prevent="sendTest">
    {{-- Start test dialog --}}
    <x-mailcoach::text-field
        :label="__mc('From')"
        :placeholder="Auth::guard(config('mailcoach.guard'))->user()->email"
        name="from_email"
        :required="true"
        type="email"
        wire:model.lazy="from_email"
    />
    <x-mailcoach::text-field
        :label="__mc('To')"
        :placeholder="Auth::guard(config('mailcoach.guard'))->user()->email"
        name="to_email"
        :required="true"
        type="email"
        wire:model.lazy="to_email"
    />
    <div class="flex items-center justify-between">
        <x-mailcoach::button :label="__mc('Send test')"/>
        <x-mailcoach::button-cancel x-on:click.prevent="$store.modals.close('send-test')" />
    </div>
</form>
