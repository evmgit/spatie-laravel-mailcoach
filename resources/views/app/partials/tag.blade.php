<a href="{{ route('mailcoach.emailLists.tags.edit', [$emailList, $tag]) }}" class="{{ $highlight ?? false ? 'tag' : 'tag-neutral' }}">
    {{ $tag->name }}
</a>
