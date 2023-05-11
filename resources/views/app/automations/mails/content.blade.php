<x-mailcoach::layout-automation-mail :title="__mc('Content')" :mail="$mail">
    <x-mailcoach::card>
        @livewire(\Livewire\Livewire::getAlias(config('mailcoach.content_editor')), [
            'model' => $mail,
        ])
    </x-mailcoach::card>
</x-mailcoach::layout-automation-mail>
