<x-mailcoach::data-table
    name="segment"
    :rows="$segments ?? null"
    :totalRowsCount="$totalSegmentsCount ?? null"
    :columns="[
        ['attribute' => 'name', 'label' => __mc('Name')],
        ['label' => __mc('Population'), 'class' => 'w-48 th-numeric'],
        ['attribute' => '-created_at', 'label' => __mc('Created at'), 'class' => 'w-48 th-numeric hidden | xl:table-cell'],
        ['class' => 'w-12'],
    ]"
    rowPartial="mailcoach::app.emailLists.segments.partials.row"
    :no-results-text="__mc('No segments found.')"
    :emptyText="__mc('A segment is a group of tags that can be targeted by an email campaign. You can <a target=\'_blank\' href=\':docsUrl\'>learn more about segmentation & tags in our docs</a>', [
        'docsUrl' => 'https://mailcoach.app/docs/self-hosted/v6/using-mailcoach/email-lists/segmentation-tags',
    ])"
>
    @slot('actions')
        @can('create', \Spatie\Mailcoach\Mailcoach::getTagSegmentClass())
            <x-mailcoach::button x-on:click="$store.modals.open('create-segment')"
                                 :label="__mc('Create segment')"/>

            <x-mailcoach::modal :title="__mc('Create segment')" name="create-segment">
                @livewire('mailcoach::create-segment', [
                    'emailList' => $emailList,
                ])
            </x-mailcoach::modal>
        @endcan
    @endslot
</x-mailcoach::data-table>
