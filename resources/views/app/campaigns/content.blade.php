<x-mailcoach::layout-campaign :title="__mc('Content')" :campaign="$campaign">
    <x-mailcoach::card>
    @livewire(Livewire::getAlias(config('mailcoach.content_editor')), [
        'model' => $campaign,
    ])
    </x-mailcoach::card>
</x-mailcoach::layout-campaign>
