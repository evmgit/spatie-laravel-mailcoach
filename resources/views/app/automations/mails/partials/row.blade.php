@php($mail ??= $row)
<tr>
    <td class="markup-links">
        <a href="{{ route('mailcoach.automations.mails.summary', $mail) }}">
            {{ $mail->name }}
        </a>
    </td>
    <td class="td-numeric">
        {{ number_format($mail->sent_to_number_of_subscribers) ?: '–' }}
    </td>
    <td class="td-numeric hidden | xl:table-cell">
        @if($mail->open_rate)
            {{ number_format($mail->unique_open_count) }}
            <div class="td-secondary-line">{{ $mail->open_rate / 100 }}%</div>
        @else
            –
        @endif
    </td>
    <td class="td-numeric hidden | xl:table-cell">
        @if($mail->click_rate)
            {{ number_format($mail->unique_click_count) }}
            <div class="td-secondary-line">{{ $mail->click_rate / 100 }}%</div>
        @else
            –
        @endif
    <td class="td-numeric hidden | xl:table-cell">
        {{ $mail->created_at->toMailcoachFormat() }}
    </td>

    <td class="td-action">
         <x-mailcoach::dropdown direction="left">
            <ul>
                <li>
                    <button wire:click.prevent="duplicateAutomationMail({{ $mail->id }})">
                        <x-mailcoach::icon-label icon="fas fa-random" :text="__mc('Duplicate')" />
                    </button>
                </li>
                <li>
                    <x-mailcoach::confirm-button
                        :confirm-text="__mc('Are you sure you want to delete email :name?', ['name' => $mail->name])"
                        onConfirm="() => $wire.deleteAutomationMail({{ $mail->id }})"
                    >
                        <x-mailcoach::icon-label icon="far fa-trash-alt" :text="__mc('Delete')" :caution="true" />
                    </x-mailcoach::confirm-button>
                </li>
            </ul>
        </x-mailcoach::dropdown>
    </td>
</tr>
