@php($sends ??= null)
@php($totalSendsCount ??= null)
<x-mailcoach::data-table
    name="send"
    :rows="$sends"
    :totalRowsCount="$totalSendsCount"
    :columns="[
        ['label' => __mc('Campaign')],
        ['label' => __mc('Opens'), 'class' => 'w-32 th-numeric'],
        ['label' => __mc('Clicks'), 'class' => 'w-32 th-numeric'],
        ['label' => __mc('Sent'), 'class' => 'w-48 th-numeric hidden | xl:table-cell'],
    ]"
    rowPartial="mailcoach::app.emailLists.subscribers.partials.sendRow"
    :emptyText="__mc('This user hasn\'t received any campaign yet.')"
/>
