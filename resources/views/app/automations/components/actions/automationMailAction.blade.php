<x-mailcoach::automation-action :index="$index" :action="$action" :editing="$editing" :editable="$editable"
                                :deletable="$deletable">
    <x-slot name="legend">
        {{__mc('Send email') }}
        <span class="form-legend-accent">
            @if ($automation_mail_id)
                @php($automationMail = \Spatie\Mailcoach\Mailcoach::getAutomationMailClass()::find($automation_mail_id))
                @if ($automationMail)
                    <a target="_blank" href="{{ route('mailcoach.automations.mails.content', $automationMail) }}">{{ optional($automationMail)->name }} <i class="text-xs fas fa-external-link-alt"></i></a>
                @endif
            @endif
        </span>
    </x-slot>

    <x-slot name="form">
        <div class="col-span-12 md:col-span-6">
            <x-mailcoach::select-field
                :label="__mc('Email')"
                name="automation_mail_id"
                wire:model="automation_mail_id"
                :options="['' => 'Select an email'] + $campaignOptions"
            />
        </div>
    </x-slot>

</x-mailcoach::automation-action>
