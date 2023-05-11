<tr class="markup-links">
    <td><a href="{{ route('mailcoach.transactionalMails.show', $row) }}">{{ $row->subject }}</a></td>
    <td class="truncate">{{ $row->toString() }}</td>
    <td class="td-numeric hidden | xl:table-cell">{{ $row->opens->count() }}</td>
    <td class="td-numeric hidden | xl:table-cell">{{ $row->clicks->count() }}</td>
    <td class="td-numeric hidden | xl:table-cell">{{ $row->created_at->toMailcoachFormat() }}</td>
    <td class="td-action">
        <x-mailcoach::confirm-button
            :confirm-text="__mc('Are you sure you want to delete this transactional mail from the log?')"
            onConfirm="() => $wire.deleteTransactionalMail({{ $row->id }})"
            class="icon-button text-red-500 hover:text-red-700"
        >
            <i class="far fa-trash-alt"></i>
        </x-mailcoach::confirm-button>
    </td>
</tr>
