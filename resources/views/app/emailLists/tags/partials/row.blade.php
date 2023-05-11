<tr>
    <td class="markup-links">
        @if ($row->type === \Spatie\Mailcoach\Domain\Campaign\Enums\TagType::Default)
            <a class="break-words" href="{{ route('mailcoach.emailLists.tags.edit', [$emailList, $row]) }}">
                {{ $row->name }}
            </a>
        @else
            {{ $row->name }}
        @endif
    </td>
    <td>
        @if ($row->visible_in_preferences)
            <x-mailcoach::rounded-icon type=success minimal size="md" icon="fas fa-check"/>
        @endif
    </td>
    <td class="td-numeric">{{ number_format($row->subscriber_count) }}</td>
    <td class="td-numeric hidden | xl:table-cell">{{ $row->updated_at->toMailcoachFormat() }}</td>

    <td class="td-action">
        <x-mailcoach::confirm-button
                onConfirm="() => $wire.deleteTag({{ $row->id }})"
                :confirm-text="__mc('Are you sure you want to delete tag :tagName?', ['tagName' => $row->name])"
                class="icon-button text-red-500 hover:text-red-700"
        >
            <i class="far fa-trash-alt"></i>
        </x-mailcoach::confirm-button>
    </td>
</tr>
