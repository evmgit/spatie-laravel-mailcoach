<x-mailcoach::data-table
    name="template"
    :modelClass="\Spatie\Mailcoach\Mailcoach::getTemplateClass()"
    :rows="$templates ?? null"
    :totalRowsCount="$totalTemplatesCount ?? null"
    :columns="[
        ['attribute' => 'name', 'label' => __mc('Name')],
        ['attribute' => 'updated_at', 'label' => __mc('Last updated'), 'class' => 'w-48 th-numeric'],
        ['class' => 'w-12'],
    ]"
    rowPartial="mailcoach::app.templates.partials.row"
    :emptyText="
        ($this->filter['search'] ?? null)
            ? __mc('No templates found.')
            : __mc('DRY? No templates here.')
    "
/>
