<x-mailcoach::data-table
    name="transactional-template"
    :createText="__mc('Create email')"
    :modelClass="\Spatie\Mailcoach\Mailcoach::getTransactionalMailClass()"
    :rows="$templates ?? null"
    :totalRowsCount="$templatesCount ?? null"
    :columns="[
        ['attribute' => 'subject', 'label' => __mc('Name')],
        ['class' => 'w-12'],
    ]"
    rowPartial="mailcoach::app.transactionalMails.templates.partials.row"
    :emptyText="__mc('You have not created any templates yet.')"
/>
