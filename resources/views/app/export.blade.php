<div class="card" @if ($exportStarted && ! $exportExists) wire:poll.750ms @endif>
    @if ($exportStarted || $exportExists)
        <h1 class="markup-h2">Export</h1>
        <x-mailcoach::fieldset class="ml-2">
            @forelse (Cache::get('export-status', []) as $name => $data)
                <p class="flex items-center gap-2">
                    @if ($data['finished'])
                        <x-mailcoach::rounded-icon size="md" type="success" icon="fas fa-check"/>
                        <strong class="font-semibold">{{ $name }}</strong>
                    @elseif ($data['error'])
                        <x-mailcoach::rounded-icon size="md" type="error" icon="fas fa-times"/>
                        <strong class="font-semibold">{{ $name }}</strong>
                        <span> &mdash; {{ $data['error'] }}</span>
                        <x-mailcoach::button-secondary class="mt-8" wire:click.prevent="newExport"
                                                       :label="__mc('Create new export')"/>
                    @else
                        <x-mailcoach::rounded-icon size="md" type="info" icon="fas fa-sync fa-spin"/>
                        <strong class="font-semibold">{{ $name }}</strong>
                    @endif
                </p>
            @empty
                <p class="flex items-center gap-2">
                    <x-mailcoach::rounded-icon size="md" type="info" icon="fas fa-sync fa-spin"/>
                    <strong class="font-semibold">{{ __mc('Export started...') }}</strong>
                </p>
            @endforelse
        </x-mailcoach::fieldset>

        @if ($exportExists)
            <div class="my-4 flex items-center gap-4">
                <x-mailcoach::button wire:click.prevent="download" :label="__mc('Download export')"/>
                <p class="text-sm">Created
                    on {{ \Illuminate\Support\Facades\Date::createFromTimestamp(Storage::disk(config('mailcoach.export_disk'))->lastModified(Spatie\Mailcoach\Http\App\Livewire\Export\ExportComponent::obfuscatedExportDirectory().'/mailcoach-export.zip'))->format('Y-m-d H:i:s') }}</p>
            </div>
            <x-mailcoach::button-secondary class="mt-8" wire:click.prevent="newExport"
                                           :label="__mc('Create new export')"/>
        @endif
    @else
        <h1 class="markup-h2">Choose which data you want to export</h1>

        <x-mailcoach::help class="mb-6">
            <p>Mailcoach can export (almost) all data to be used in a different Mailcoach instance.</p>
            <p>The exporter will <strong>not export</strong> the following data:</p>
            <ul class="list-disc ml-4">
                <li>Users</li>
                <li>Individual send data</li>
                <li>Clicks / Opens / Unsubscribes (it will only export the calculated statistics)</li>
                <li>Any uploaded media</li>
            </ul>
        </x-mailcoach::help>

        <div class="flex items-baseline gap-2 mb-2">
            <h2 class="form-legend">
                Email lists
            </h2>
            <a class="text-blue-500 text-sm underline" href="#" wire:click.prevent="selectAllEmailLists">All</a>
        </div>

        <p class="mb-3">This includes subscribers, tags & segments</p>
        <div class="flex flex-col gap-4 mb-6">
            @foreach($emailLists as $id => $name)
                <x-mailcoach::checkbox-field
                        name="selectedEmailList-{{ $id }}"
                        value="{{ $id }}"
                        :label="$name"
                        wire:model="selectedEmailLists"
                />
            @endforeach
        </div>

        <div class="flex items-baseline gap-2 mb-2">
            <h2 class="form-legend">
                Campaigns
            </h2>
            <a class="text-blue-500 text-sm underline" href="#" wire:click.prevent="selectAllCampaigns">All</a>
        </div>
        <div class="flex flex-col gap-4 mb-6">
            @forelse($campaigns as $id => $name)
                <x-mailcoach::checkbox-field
                        name="selectedCampaign-{{ $id }}"
                        value="{{ $id }}"
                        :label="$name"
                        wire:model="selectedCampaigns"
                />
            @empty
                <x-mailcoach::info>No campaigns found, campaigns require their email list to be exported as well.
                </x-mailcoach::info>
            @endforelse
        </div>

        <div class="flex items-baseline gap-2 mb-2">
            <h2 class="form-legend">
                Templates
            </h2>
            <a class="text-blue-500 text-sm underline" href="#" wire:click.prevent="selectAllTemplates">All</a>
        </div>
        <div class="flex flex-col gap-4 mb-6">
            @forelse($templates as $id => $name)
                <x-mailcoach::checkbox-field
                        name="selectedTemplate-{{ $id }}"
                        value="{{ $id }}"
                        :label="$name"
                        wire:model="selectedTemplates"
                />
            @empty
                <x-mailcoach::info>No templates found.</x-mailcoach::info>
            @endforelse
        </div>

        <div class="flex items-baseline gap-2 mb-2">
            <h2 class="form-legend">
                Automations
            </h2>
            <a class="text-blue-500 text-sm underline" href="#" wire:click.prevent="selectAllAutomations">All</a>
        </div>
        <p class="mb-3">This includes triggers, actions & action-subscriber state</p>

        <x-mailcoach::warning class="mb-3">"Send automation mail" actions will need manual adjustment to the correct
            Automation Mail.
        </x-mailcoach::warning>

        <div class="flex flex-col gap-4 mb-6">
            @forelse($automations as $id => $name)
                <x-mailcoach::checkbox-field
                        name="selectedAutomation-{{ $id }}"
                        value="{{ $id }}"
                        :label="$name"
                        wire:model="selectedAutomations"
                />
            @empty
                <x-mailcoach::info>No automations found, automations require their email list to be exported as well.
                </x-mailcoach::info>
            @endforelse
        </div>

        <div class="flex items-baseline gap-2 mb-2">
            <h2 class="form-legend">Automation Mails</h2>
            <a class="text-blue-500 text-sm underline" href="#" wire:click.prevent="selectAllAutomationMails">All</a>
        </div>
        <div class="flex flex-col gap-4 mb-6">
            @forelse($automationMails as $id => $name)
                <x-mailcoach::checkbox-field
                        name="selectedAutomationMail-{{ $id }}"
                        value="{{ $id }}"
                        :label="$name"
                        wire:model="selectedAutomationMails"
                />
            @empty
                <x-mailcoach::info>No automation mails found.</x-mailcoach::info>
            @endforelse
        </div>

        <div class="flex items-baseline gap-2 mb-2">
            <h2 class="form-legend">
                Transactional Mail Templates</h2>
            <a class="text-blue-500 text-sm underline" href="#"
               wire:click.prevent="selectAllTransactionalMailTemplates">All</a>
        </div>
        <div class="flex flex-col gap-4 mb-6">
            @forelse($transactionalMailTemplates as $id => $name)
                <x-mailcoach::checkbox-field
                        name="selectedTransactionalMailTemplate-{{ $id }}"
                        value="{{ $id }}"
                        :label="$name"
                        wire:model="selectedTransactionalMailTemplates"
                />
            @empty
                <x-mailcoach::info>No transactional mail templates found.</x-mailcoach::info>
            @endforelse
        </div>

        <x-mailcoach::form-buttons>
            <x-mailcoach::button wire:click.prevent="export" wire:loading.attr="disabled" :label="__mc('Export')"/>
        </x-mailcoach::form-buttons>
    @endif
</div>
