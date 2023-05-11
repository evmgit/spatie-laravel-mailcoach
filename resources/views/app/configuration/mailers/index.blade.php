<x-mailcoach::data-table
    name="mailer"
    :modelClass="config('mailcoach-ui.models.mailer', \Spatie\Mailcoach\Domain\Settings\Models\Mailer::class)"
    :rows="$mailers ?? null"
    :totalRowsCount="$totalMailersCount ?? null"
    :columns="[
        ['attribute' => 'name', 'label' => __mc('Name'), 'class' => 'w-64'],
        ['attribute' => 'transport', 'label' => __mc('Transport'), 'class' => 'w-48'],
        ['attribute' => 'ready_for_use', 'label' => __mc('Ready for use'), 'class' => 'w-48'],
        ['attribute' => 'default', 'label' => __mc('Default'), 'class' => 'w-48'],
        ['class' => 'w-12'],
    ]"
    rowPartial="mailcoach::app.configuration.mailers.partials.row"
    :emptyText="__mc('No mailers')"
/>
