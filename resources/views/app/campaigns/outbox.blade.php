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
    rowPartial="mailcoach::app.campaigns.partials.outboxRow"
>
    @slot('actions')
        @if (($totalFailed = $this->campaign->sends()->failed()->count()) > 0)
            <div class="table-actions">
                <x-mailcoach::confirm-button
                    method="POST"
                    data-confirm="true"
                    onConfirm="() => $wire.retryFailedSends()"
                    :confirm-text="__mc('Are you sure you want to resend :totalFailed mails?', ['totalFailed' => $totalFailed])"
                    class="mt-4 button"
                >
                    {{ __mc('Try resending :totalFailed :email', ['totalFailed' => $totalFailed, 'email' => __mc_choice('email|emails', $totalFailed)]) }}
                </x-mailcoach::confirm-button>
            </div>
        @endif
    @endslot
</x-mailcoach::data-table>
