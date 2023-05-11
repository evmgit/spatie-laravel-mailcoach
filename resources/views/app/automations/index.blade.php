<x-mailcoach::data-table
    name="automation"
    :modelClass="\Spatie\Mailcoach\Mailcoach::getAutomationClass()"
    :rows="$automations ?? null"
    :totalRowsCount="$totalAutomationsCount ?? null"
    :columns="[
        ['class' => 'w-4'],
        ['attribute' => 'name', 'label' => __mc('Name')],
        ['attribute' => '-updated_at', 'label' => __mc('Last updated'), 'class' => 'w-48 th-numeric'],
        ['class' => 'w-12'],
    ]"
    rowPartial="mailcoach::app.automations.partials.row"
    :emptyText="__mc('No automations yet. A welcome automation is a good start!')"
    :noResultsText="__mc('No automations found.')"
/>
