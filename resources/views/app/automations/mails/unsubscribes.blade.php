<x-mailcoach::data-table
    name="unsubscribe"
    :rows="$unsubscribes ?? null"
    :totalRowsCount="$totalUnsubscribes ?? null"
    rowPartial="mailcoach::app.automations.mails.partials.unsubscribeRow"
    :emptyText="__mc('No unsubscribes have been received yet.')"
    :columns="[
        ['label' => __mc('Email')],
        ['attribute' => '-created_at', 'label' => __mc('Date'), 'class' => 'w-48 th-numeric hidden | xl:table-cell'],
    ]"
/>
