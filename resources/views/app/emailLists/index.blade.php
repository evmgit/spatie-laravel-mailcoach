<x-mailcoach::data-table
    name="list"
    :modelClass="\Spatie\Mailcoach\Mailcoach::getEmailListClass()"
    :rows="$emailLists ?? null"
    :totalRowsCount="$totalEmailListsCount ?? null"
    :columns="[
        ['attribute' => 'name', 'label' => __mc('Name')],
        ['attribute' => '-active_subscribers_count', 'label' => __mc('Active'), 'class' => 'w-32 th-numeric'],
        ['attribute' => '-created_at', 'label' => __mc('Created'), 'class' => 'w-48 th-numeric hidden | xl:table-cell'],
        ['class' => 'w-12'],
    ]"
    rowPartial="mailcoach::app.emailLists.partials.row"
    :emptyText="
        ($this->filter['search'] ?? null)
            ? __mc('No email lists found.')
            : __mc('You\'ll need at least one list to gather subscribers.')
    "
/>
