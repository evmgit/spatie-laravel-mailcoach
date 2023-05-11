<x-mailcoach::card>
    <x-mailcoach::help>
        <div class="markup-lists">
        <p>Mailcoach can import (almost) all data to be used in a different Mailcoach instance.</p>
        <p>The import will <strong class="font-semibold">not import</strong> the following data:</p>
        <ul>
            <li>Users</li>
            <li>Individual send data</li>
            <li>Clicks / Opens / Unsubscribes (it will only export the calculated statistics)</li>
            <li>Any uploaded media</li>
        </ul>
        <p>Be sure to check your automations after import:</p>
        <ul>
            <li><strong class="font-semibold">"Send automation mail"</strong> actions will need manual adjustment to the correct Automation Mail</li>
            <li>Automations are imported <strong class="font-semibold">as paused.</strong></li>
        </ul>
        <p>Imports can always be reuploaded if something goes wrong.</p>
        </div>
    </x-mailcoach::help>


    @if (($steps = Cache::get('import-status', [])) || $importStarted)
        <x-mailcoach::fieldset class="ml-2">
        <div class="flex flex-col gap-4" @if(! collect($steps)->where('failed', true)->count() && ! collect($steps)->keys()->contains('Cleanup')) wire:poll.1500ms @endif>
            @forelse ($steps as $name => $data)
                <p class="flex items-center gap-2">
                    @if ($data['finished'])
                        <x-mailcoach::rounded-icon size="md" type="success" icon="fas fa-check" />
                        <strong class="font-semibold">{{ $name }}</strong>
                        @if($data['total'])
                            <span>&mdash; {{ number_format($data['total']) }} rows</span>
                        @endif
                    @elseif ($data['failed'])
                        <x-mailcoach::rounded-icon size="md" type="error" icon="fas fa-times" />
                        <strong class="font-semibold">{{ $name }}</strong>
                        <span> &mdash; {{ $data['message'] }}</span>
                    @else
                        <x-mailcoach::rounded-icon size="md" type="info" icon="fas fa-sync fa-spin" />
                        <strong class="font-semibold">{{ $name }}</strong>
                        <span>({{ round($data['progress'] * 100, 2) }}%)</span>
                    @endif
                </p>
            @empty
                <p class="flex items-center gap-2">
                    <x-mailcoach::rounded-icon size="md" type="success" icon="fas fa-check" />
                    <strong class="font-semibold">Import queued...</strong>
                </p>
            @endforelse

            @if(!collect($steps)->where('finished', false)->where('failed', false)->count() && !collect($steps)->keys()->contains('Cleanup'))
                <div class="flex items-center gap-2">
                    <x-mailcoach::rounded-icon size="md" type="info" icon="fas fa-sync fa-spin" />
                    <strong class="font-semibold">Next step is queued...</strong>
                </div>
            @endif
        </div>
        </x-mailcoach::fieldset>

        <x-mailcoach::button wire:click.prevent="clear" :label="__mc('Start new import')" />
    @else
        <div class="flex gap-6">
            <div>
                <input accept=".zip" type="file" wire:model="file" />
                @error('file')
                <p class="form-error">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex items-center h-10">
                <i class="far fa-arrow-right text-blue-300"></i>
            </div>
            <div class="flex items-center gap-4">
                <x-mailcoach::button wire:click.prevent="import" :label="__mc('Import')" :disabled="!$file" />
                <div wire:loading wire:target="file">
                    <style>
                        @keyframes loadingpulse {
                            0%   {transform: scale(.8); opacity: .75}
                            100% {transform: scale(1); opacity: .9}
                        }
                    </style>
                    <span
                        style="animation: loadingpulse 0.75s alternate infinite ease-in-out;"
                        class="group w-8 h-8 inline-flex items-center justify-center bg-gradient-to-b from-blue-500 to-blue-600 text-white rounded-full">
                        <span class="flex items-center justify-center w-6 h-6 transform group-hover:scale-90 transition-transform duration-150">
                            @include('mailcoach::app.layouts.partials.logoSvg')
                        </span>
                    </span>
                    <span class="ml-1 text-gray-700">Uploading...</span>
                </div>
            </div>
        </div>
    @endif
</x-mailcoach::card>
