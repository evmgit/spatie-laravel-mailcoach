@if ($totalListsCount ?? 0)
    @php($emptyText = __mc('No campaigns yet. Go write something!'))
@else
    @php($emptyText = __mc('No campaigns yet, but you‘ll need a list first, go <a href=":emailListsLink">create one</a>!', ['emailListsLink' => route('mailcoach.emailLists')]))
@endif
<x-mailcoach::data-table
    name="campaign"
    :rows="$campaigns ?? null"
    :totalRowsCount="$totalCampaignsCount ?? null"
    :model-class="\Spatie\Mailcoach\Mailcoach::getCampaignClass()"
    :filters="[
        ['attribute' => 'status', 'value' => '', 'label' => __mc('All'), 'count' => $totalCampaignsCount ?? null],
        ['attribute' => 'status', 'value' => 'sent', 'label' => __mc('Sent'), 'count' => $sentCampaignsCount ?? null],
        ['attribute' => 'status', 'value' => 'scheduled', 'label' => __mc('Scheduled'), 'count' => $scheduledCampaignsCount ?? null],
        ['attribute' => 'status', 'value' => 'draft', 'label' => __mc('Draft'), 'count' => $draftCampaignsCount ?? null],
    ]"
    :columns="[
        ['class' => 'w-4'],
        ['attribute' => 'name', 'label' => __mc('Name')],
        ['attribute' => 'email_list_id', 'label' => __mc('List'), 'class' => 'w-48'],
        ['attribute' => '-sent_to_number_of_subscribers', 'label' => __mc('Emails'), 'class' => 'w-24 th-numeric'],
        ['attribute' => '-unique_open_count', 'label' => __mc('Opens'), 'class' => 'w-24 th-numeric hidden | xl:table-cell'],
        ['attribute' => '-unique_click_count', 'label' => __mc('Clicks'), 'class' => 'w-24 th-numeric hidden | xl:table-cell'],
        ['attribute' => '-sent', 'label' => __mc('Sent'), 'class' => 'w-48 th-numeric hidden | xl:table-cell'],
        ['class' => 'w-12'],
    ]"
    rowPartial="mailcoach::app.campaigns.partials.row"
    :emptyText="$emptyText"
    :noResultsText="__mc('No campaigns found.')"
/>
