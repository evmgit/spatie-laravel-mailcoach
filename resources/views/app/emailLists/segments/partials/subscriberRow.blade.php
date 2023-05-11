<tr>
    <td class="markup-links">
        <a class="break-words" href="{{ route('mailcoach.emailLists.subscriber.details', [$emailList, $row]) }}">
            {{ $row->email }}
        </a>
        <div class="td-secondary-line">
            {{ $row->first_name }} {{ $row->last_name }}
        </div>
    </td>
    <td>
        @foreach($row->tags->where('type', \Spatie\Mailcoach\Domain\Campaign\Enums\TagType::Default) as $tag)
            @include('mailcoach::app.partials.tag')
        @endforeach
    </td>
</tr>
