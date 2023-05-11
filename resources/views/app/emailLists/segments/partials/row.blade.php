<tr>
    <td class="markup-links">
        <a class="break-words" href="{{ route('mailcoach.emailLists.segments.edit', [$row->emailList, $row]) }}">
            {{ $row->name }}
        </a>
    </td>
    <td class="td-numeric">
        {{ number_format($row->getSubscribersQuery()->count()) }}
    </td>
    <td class="td-numeric hidden | xl:table-cell">{{ $row->created_at->toMailcoachFormat() }}</td>
    <td class="td-action">
        <x-mailcoach::dropdown direction="left">
            <ul>
                <li>
                    <button wire:click.prevent="duplicateSegment({{ $row->id }})">
                        <x-mailcoach::icon-label icon="fas fa-random" :text="__mc('Duplicate')" />
                    </button>
                </li>
                <li>
                    <x-mailcoach::confirm-button
                        :confirm-text="__mc('Are you sure you want to delete segment :segmentName?', ['segmentName' => $row->name])"
                        onConfirm="() => $wire.deleteSegment({{ $row->id }})"
                    >
                        <x-mailcoach::icon-label icon="far fa-trash-alt" :text="__mc('Delete')" :caution="true"/>
                    </x-mailcoach::confirm-button>
                </li>
            </ul>
        </x-mailcoach::dropdown>
    </td>
</tr>
