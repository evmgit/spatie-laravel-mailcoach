<x-mailcoach::data-table
    name="transactional-mail"
    :rows="$transactionalMails ?? null"
    :totalRowsCount="$transactionalMailsCount ?? null"
    :columns="[
        ['attribute' => 'subject', 'label' => __mc('Subject')],
        ['label' => __mc('To')],
        ['label' => __mc('Opens'), 'class' => 'w-24 th-numeric hidden | xl:table-cell'],
        ['label' => __mc('Clicks'), 'class' => 'w-24 th-numeric hidden | xl:table-cell'],
        ['attribute' => '-created_at', 'label' => __mc('Sent'), 'class' => 'w-56 th-numeric hidden | xl:table-cell'],
        ['class' => 'w-12'],
    ]"
    rowPartial="mailcoach::app.transactionalMails.row"
    :emptyText="__mc('No transactional mails have been sent yet!')"
/>
