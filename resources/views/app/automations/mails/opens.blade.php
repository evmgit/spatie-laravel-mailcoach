<div>
    @if($mail->open_count)
        <x-mailcoach::data-table
            name="open"
            :rows="$mailOpens ?? null"
            :totalRowsCount="$totalMailOpensCount ?? null"
            :columns="[
                ['attribute' => 'email', 'label' => __mc('Email')],
                ['attribute' => 'open_count', 'label' => __mc('Opens'), 'class' => 'w-32 th-numeric'],
                ['attribute' => '-first_opened_at', 'label' => __mc('First opened at'), 'class' => 'w-48 th-numeric hidden | xl:table-cell'],
            ]"
            rowPartial="mailcoach::app.automations.mails.partials.openRow"
            :emptyText="__mc('No opens yet. Stay tuned.')"
        />
    @else
        <x-mailcoach::card>
            <x-mailcoach::info>
                {{ __mc('No opens tracked') }}
            </x-mailcoach::info>
        </x-mailcoach::card>
    @endif
</div>
