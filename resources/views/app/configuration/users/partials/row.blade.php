<tr>
    <td class="markup-links">
        <a href="{{ $row->id === auth()->guard(config('mailcoach.guard'))->user()->id ? route('account') : route('users.edit', $row) }}">
            {{ $row->email }}
        </a>
    </td>
    <td>{{ $row->name }}</td>
    <td class="td-action">
        @if ($row->id !== auth()->guard(config('mailcoach.guard'))->user()->id)
            <x-mailcoach::confirm-button
                :confirm-text="__mc('Are you sure you want to delete user: :user?', ['user' => $row->name])"
                onConfirm="() => $wire.deleteUser({{ $row->id }})"
            >
                <x-mailcoach::icon-label icon="far fa-trash-alt" :caution="true" />
            </x-mailcoach::confirm-button>
        @endif
    </td>
</tr>
