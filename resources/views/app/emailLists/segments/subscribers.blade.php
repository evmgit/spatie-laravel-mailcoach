<div class="card-grid">
<x-mailcoach::card class="py-4">
    <x-mailcoach::info>
        @if(($selectedSubscribersCount ?? null) && ($subscribersCount ?? null))
            {!! __mc('Population is <strong>:percentage%</strong> of list total of :subscribersCount.', ['percentage' => round($selectedSubscribersCount / $subscribersCount * 100 , 2), 'subscribersCount' => number_format($subscribersCount)]) !!}
        @else
            {{ __mc('Loading') }}...
        @endif
    </x-mailcoach::info>
</x-mailcoach::card>
<x-mailcoach::data-table
    name="subscriber"
    :rows="$subscribers ?? null"
    :totalRowsCount="$selectedSubscribersCount ?? null"
    :columns="[
        ['attribute' => 'email', 'label' => __mc('Email')],
        ['label' => __mc('Tags')],
    ]"
    rowPartial="mailcoach::app.emailLists.segments.partials.subscriberRow"
    :rowData="[
        'emailList' => $emailList,
    ]"
    :emptyText="__mc('This is a very exclusive segment. Nobody got selected.')"
    :searchable="false"
/>
</div>
