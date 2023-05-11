<x-mailcoach::data-table
    name="automation-mail"
    :create-text="__('Create automation mail')"
    :modelClass="\Spatie\Mailcoach\Mailcoach::getAutomationMailClass()"
    :rows="$automationMails ?? null"
    :totalRowsCount="$totalAutomationMailsCount ?? null"
    :columns="[
        ['attribute' => 'name', 'label' => __mc('Name')],
        ['attribute' => '-sent_to_number_of_subscribers', 'label' => __mc('Emails'), 'class' => 'w-24 th-numeric'],
        ['attribute' => '-unique_open_count', 'label' => __mc('Opens'), 'class' => 'w-24 th-numeric hidden | xl:table-cell'],
        ['attribute' => '-unique_click_count', 'label' => __mc('Clicks'), 'class' => 'w-24 th-numeric hidden | xl:table-cell'],
        ['attribute' => '-created_at', 'class' => 'w-48 th-numeric hidden | xl:table-cell', 'label' => __mc('Created at')],
        ['class' => 'w-12'],
    ]"
    rowPartial="mailcoach::app.automations.mails.partials.row"
    :emptyText="__mc('No automated mails yet.')"
/>
