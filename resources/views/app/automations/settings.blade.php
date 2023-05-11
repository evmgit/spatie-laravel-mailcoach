<form
    class="card-grid"
    wire:submit.prevent="save(new URLSearchParams(new FormData($event.target)).toString())"
    method="POST"
    novalidate
>
    <x-mailcoach::card>
        <x-mailcoach::text-field :label="__mc('Name')" name="automation.name" wire:model.lazy="automation.name" required />

        <div class="form-field gap-y-4 flex flex-col">
            <label class="label" for="automation.repeat_enabled">
                {{ __mc('Repeat') }}
            </label>

            <x-mailcoach::checkbox-field
                :label="__mc('Allow for subscribers to go through the automation more than once')"
                name="automation.repeat_enabled"
                wire:model.lazy="automation.repeat_enabled"
            />

            @if ($automation->repeat_enabled)
                <x-mailcoach::checkbox-field
                    :label="__mc('Repeat only when subscriber was halted')"
                    name="automation.repeat_only_after_halt"
                    wire:model.lazy="automation.repeat_only_after_halt"
                />
            @endif
        </div>

        <x-mailcoach::select-field
            :label="__mc('Trigger')"
            name="selectedTrigger"
            :options="$triggerOptions"
            placeholder="Select a trigger"
            required
            wire:model="selectedTrigger"
        />

        <div>
            @if ($selectedTrigger && $selectedTrigger::getComponent())
                @livewire($selectedTrigger::getComponent(), [
                    'triggerClass' => $automation->triggerClass(),
                    'automation' => $automation,
                ], key($selectedTrigger))
            @endif
        </div>
    </x-mailcoach::card>
    @include('mailcoach::app.campaigns.partials.emailListFields', ['segmentable' => $automation, 'wiremodel' => 'automation'])

    <x-mailcoach::fieldset card :legend="__mc('Usage in Mailcoach API')">
        <div>
            <x-mailcoach::help>
                {!! __mc('Whenever you need to specify a <code>:resourceName</code> in the Mailcoach API and want to use this :resource, you\'ll need to pass this value', [
                'resourceName' => 'automation uuid',
                'resource' => 'automation',
            ]) !!}
                <p class="mt-4">
                    <x-mailcoach::code-copy class="flex items-center justify-between max-w-md" :code="$automation->uuid"></x-mailcoach::code-copy>
                </p>
            </x-mailcoach::help>
        </div>
    </x-mailcoach::fieldset>

    <x-mailcoach::card buttons>
        <x-mailcoach::button :label="__mc('Save')" />
    </x-mailcoach::card>
</form>
