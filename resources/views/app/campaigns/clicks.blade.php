<div>
    @if($campaign->click_count)
        <x-mailcoach::data-table
            name="clicks"
            :rows="$links ?? null"
            :totalRowsCount="$totalLinksCount ?? null"
            :columns="array_filter([
                ['attribute' => 'link', 'label' => __mc('Link')],
                $campaign->add_subscriber_link_tags ? ['label' => __mc('Tag')] : null,
                ['attribute' => '-unique_click_count', 'label' => __mc('Unique Clicks'), 'class' => 'w-32 th-numeric hidden | xl:table-cell'],
                ['attribute' => '-click_count', 'label' => __mc('Clicks'), 'class' => 'w-32 th-numeric'],
            ])"
            rowPartial="mailcoach::app.campaigns.partials.clickRow"
            :rowData="[
                'campaign' => $campaign,
            ]"
            :emptyText="__mc('No clicks yet. Stay tuned.')"
        />
    @else
        <x-mailcoach::card>
            <x-mailcoach::info>
                {{ __mc('No clicks tracked') }}
            </x-mailcoach::info>
        </x-mailcoach::card>
    @endif
</div>
