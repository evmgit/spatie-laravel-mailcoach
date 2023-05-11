<x-mailcoach::data-table
    name="user"
    :modelClass="\Spatie\Mailcoach\Domain\Settings\Models\User::class"
    :rows="$users ?? null"
    :totalRowsCount="$totalUsersCount ?? null"
    :columns="[
        ['attribute' => 'email', 'label' => __mc('Email')],
        ['attribute' => 'name', 'label' => __mc('Name')],
        ['class' => 'w-12'],
    ]"
    rowPartial="mailcoach::app.configuration.users.partials.row"
    :emptyText="__mc('No users')"
/>
