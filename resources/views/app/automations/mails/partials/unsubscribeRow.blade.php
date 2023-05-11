<tr>
    <td class="markup-links">
        <a class="break-words" href="{{ route('mailcoach.emailLists.subscriber.details', [$row->subscriber->emailList, $row->subscriber]) }}">
            {{ $row->subscriber->email }}
        </a>
        <div class="td-secondary-line">
            {{ $row->subscriber->first_name }} {{ $row->subscriber->last_name }}
        </div>
    </td>
    <td class="td-numeric hidden | xl:table-cell">{{ $row->created_at->toMailcoachFormat() }}</td>
</tr>
