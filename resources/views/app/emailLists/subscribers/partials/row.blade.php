<tr>
    @if ($selectable)
        <td class="text-xs !pt-4">
            <x-mailcoach::checkbox-field name="select-{{ $row->id }}" label="" :checked="in_array($row->id, $this->selectedRows)" wire:change="select('{{ $row->id }}')" />
        </td>
    @endif
    <td class="markup-links">
        @if ($row->isUnconfirmed())
            <x-mailcoach::rounded-icon minimal size="md" type="warning" icon="fas fa-question" title="{{ __mc('Unconfirmed') }}"/>
        @endif
        @if ($row->isSubscribed())
            <x-mailcoach::rounded-icon minimal size="md" type="success" icon="far fa-check" title="{{ __mc('Subscribed') }}"/>
        @endif
        @if ($row->isUnsubscribed())
            <x-mailcoach::rounded-icon minimal size="md" type="neutral" icon="far fa-ban" title="{{ __mc('Unsubscribed') }}"/>
        @endif
    </td>
    <td class="markup-links leading-tight">
        <a class="break-words"
           href="{{ route('mailcoach.emailLists.subscriber.details', [$row->emailList, $row]) }}">
            {{ $row->email }}
        </a>
        <div class="td-secondary-line break-words">
            {{ $row->first_name }} {{ $row->last_name }}
        </div>
    </td>
    <td class="hidden | xl:table-cell">
        @foreach($row->tags->where('type', \Spatie\Mailcoach\Domain\Campaign\Enums\TagType::Default) as $tag)
            @include('mailcoach::app.partials.tag', [
                'emailList' => $row->emailList,
                'highlight' => $this->search && str_contains($tag->name, $this->search),
            ])
        @endforeach
    </td>
    <td class="td-numeric hidden | xl:table-cell">{{
    $row->isUnsubscribed()
    ? $row->unsubscribed_at?->toMailcoachFormat()
    : $row->subscribed_at?->toMailcoachFormat() }}</td>
    <td class="td-action">
        <x-mailcoach::dropdown direction="left">
            <ul>
                @if ($row->isUnconfirmed())
                    <li>
                        <x-mailcoach::confirm-button
                            onConfirm="() => $wire.resendConfirmation({{ $row->id }})"
                            :confirm-text="__mc('Are you sure you want to resend the confirmation mail :email?', ['email' => $row->email])"
                        >
                            <x-mailcoach::icon-label icon="fa-fw far fa-envelope"
                                                     :text="__mc('Resend confirmation mail')"/>
                        </x-mailcoach::confirm-button>
                    </li>
                    <li>
                        <x-mailcoach::confirm-button
                            onConfirm="() => $wire.confirm({{ $row->id }})"
                            :confirm-text="__mc('Are you sure you want to confirm :email?', ['email' => $row->email])"
                        >
                            <x-mailcoach::icon-label icon="fa-fw fas fa-check" :text="__mc('Confirm')"/>
                        </x-mailcoach::confirm-button>
                    </li>
                @endif
                @if ($row->isSubscribed())
                    <li>
                        <x-mailcoach::confirm-button
                            onConfirm="() => $wire.unsubscribe({{ $row->id }})"
                            :confirm-text="__mc('Are you sure you want to unsubscribe :email?', ['email' => $row->email])"
                        >
                            <x-mailcoach::icon-label icon="fa-fw fas fa-ban" :text="__mc('Unsubscribe')"/>
                        </x-mailcoach::confirm-button>
                    </li>
                @endif
                @if ($row->isUnsubscribed())
                    <li>
                        <x-mailcoach::confirm-button
                            onConfirm="() => $wire.resubscribe({{ $row->id }})"
                            :confirm-text="__mc('Are you sure you want to resubscribe :email?', ['email' => $row->email])"
                        >
                            <x-mailcoach::icon-label icon="fa-fw fas fa-redo" :text="__mc('Resubscribe')"/>
                        </x-mailcoach::confirm-button>
                    </li>
                @endif
                <li>
                    <x-mailcoach::confirm-button
                        :confirm-text="__mc('Are you sure you want to delete subscriber :email?', ['email' => $row->email])"
                        onConfirm="() => $wire.deleteSubscriber({{ $row->id }})"
                    >
                        <x-mailcoach::icon-label icon="fa-fw far fa-trash-alt" :text="__mc('Delete')"
                                                 :caution="true"/>
                    </x-mailcoach::confirm-button>
                </li>
            </ul>
        </x-mailcoach::dropdown>
    </td>
</tr>
