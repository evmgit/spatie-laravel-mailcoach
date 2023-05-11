<x-mailcoach::data-table
    name="sends"
    :rows="$sends ?? null"
    :totalRowsCount="$totalSends ?? null"
    :filters="[
        ['attribute' => 'type', 'value' => '', 'label' => __mc('All'), 'count' => $totalSends ?? null],
        ['attribute' => 'type', 'value' => 'pending', 'label' => __mc('Pending'), 'count' => $totalPending ?? null],
        ['attribute' => 'type', 'value' => 'failed', 'label' => __mc('Failed'), 'count' => $totalFailed ?? null],
        ['attribute' => 'type', 'value' => 'sent', 'label' => __mc('Sent'), 'count' => $totalSent ?? null],
        ['attribute' => 'type', 'value' => 'bounced', 'label' => __mc('Bounced'), 'count' => $totalBounces ?? null],
        ['attribute' => 'type', 'value' => 'complained', 'label' => __mc('Complaints'), 'count' => $totalComplaints ?? null],
    ]"
    :columns="[
        ['attribute' => 'subscriber_email', 'label' => __mc('Email address')],
        ['attribute' => 'subscriber_email', 'label' => __mc('Problem')],
        ['attribute' => '-sent_at', 'label' => __mc('Sent at'), 'class' => 'w-48 th-numeric hidden | xl:table-cell'],
    ]"
    rowPartial="mailcoach::app.automations.mails.partials.outboxRow"
/>
