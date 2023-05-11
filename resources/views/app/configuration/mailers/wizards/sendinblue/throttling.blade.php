<div>
@include('mailcoach::app.configuration.mailers.wizards.wizardNavigation')
<x-mailcoach::card>

    <x-mailcoach::help>
        <p>In order not to overwhelm your provider with send requests, Mailcoach will throttle the amount of mails sent.</p>
        <p>You can find more info about sending limits in <a href="https://help.sendinblue.com/hc/en-us/articles/360022153079-Are-there-any-sending-limits-emails-and-SMS-" target="_blank">the Sendinblue documentation</a></p>
    </x-mailcoach::help>

        <form class="form-grid" wire:submit.prevent="submit">

            <div class="flex items-center gap-x-2">
                <span>{{ __mc('Send') }}</span>
                <x-mailcoach::text-field
                    wrapper-class="w-32"
                    wire:model.lazy="mailsPerTimeSpan"
                    label=""
                    name="mailsPerTimeSpan"
                    type="number"
                />
                <span>{{ __mc('mails every') }}</span>
                <x-mailcoach::text-field
                    wrapper-class="w-32"
                    wire:model.lazy="timespanInSeconds"
                    label=""
                    name="timespanInSeconds"
                    type="number"
                />
                <span>{{ __mc_choice('second|seconds', $timespanInSeconds) }}</span>
            </div>

            <x-mailcoach::form-buttons>
                <x-mailcoach::button :label="__mc('Save')"/>
        </x-mailcoach::form-buttons>
        </form>
    </x-mailcoach::card>
</div>
