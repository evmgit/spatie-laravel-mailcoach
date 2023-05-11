<tr>
    <td class="markup-links">
        <a class="break-words" href="{{ route('mailcoach.emailLists.subscriber.details', [$row->subscriber_email_list_id, $row->subscriber_id]) }}">
            @if (config('mailcoach.encryption.enabled'))
                @php
                    $subscriber = (new \Spatie\Mailcoach\Domain\Audience\Models\Subscriber(['email' => $row->subscriber_email, 'first_name' => null, 'last_name' => null]));
                    $subscriber->decryptRow()
                @endphp
                {{ $subscriber->email }}
            @else
                {{ $row->subscriber_email }}
            @endif
        </a>
    </td>
    <td class="td-numeric">{{ $row->open_count }}</td>
    <td class="td-numeric hidden | xl:table-cell">{{ $row->first_opened_at->toMailcoachFormat() }}</td>
</tr>
