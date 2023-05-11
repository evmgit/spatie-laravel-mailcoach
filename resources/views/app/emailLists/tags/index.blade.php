@php($tags ??= null)
@php($totalTagsCount ??= null)
<x-mailcoach::data-table
    name="tag"
    :rows="$tags"
    :totalRowsCount="$totalTagsCount"
    :filters="[
        ['attribute' => 'type', 'value' => '', 'label' => __mc('All'), 'count' => $totalTagsCount ?? null],
        ['attribute' => 'type', 'value' => \Spatie\Mailcoach\Domain\Campaign\Enums\TagType::Default, 'label' => __mc('Default'), 'count' => $totalDefault ?? null],
        ['attribute' => 'type', 'value' => \Spatie\Mailcoach\Domain\Campaign\Enums\TagType::Mailcoach, 'label' => __mc('Mailcoach'), 'count' => $totalMailcoach ?? null],
    ]"
    :columns="[
        ['attribute' => 'name', 'label' => __mc('Name')],
        ['attribute' => 'visible_in_preferences', 'label' => __mc('Visible')],
        ['attribute' => 'subscriber_count', 'label' => __mc('Subscribers'), 'class' => 'w-32 th-numeric'],
        ['attribute' => 'updated_at', 'label' => __mc('Updated at'), 'class' => 'w-48 th-numeric hidden | xl:table-cell'],
        ['class' => 'w-12'],
    ]"
    rowPartial="mailcoach::app.emailLists.tags.partials.row"
    :rowData="['emailList' => $emailList]"
    :emptyText="__mc('There are no tags for this list. Everyone is equal!')"
>
    @slot('actions')
        @can('create', \Spatie\Mailcoach\Mailcoach::getTagClass())
            <x-mailcoach::button x-on:click="$store.modals.open('create-tag')" :label="__mc('Create tag')"/>

            <x-mailcoach::modal :title="__mc('Create tag')" name="create-tag">
                @livewire('mailcoach::create-tag', [
                    'emailList' => $emailList,
                ])
            </x-mailcoach::modal>
        @endcan
    @endslot
</x-mailcoach::data-table>
