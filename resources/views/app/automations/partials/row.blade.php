@php($automation ??= $row)
<tr>
    <td class="group align-center">
        <div class="w-5 h-5">
            <button wire:click.prevent="toggleAutomationStatus({{ $automation->id }})">
                @if($automation->status === \Spatie\Mailcoach\Domain\Automation\Enums\AutomationStatus::Paused)
                    <span class="group-hover:opacity-0 fas fa-magic text-gray-400"></span>
                    <span title="{{ __mc('Start Automation') }}"
                          class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100">
                        <x-mailcoach::rounded-icon class="w-5 h-5" type="success" icon="fas fa-play"/>
                    </span>
                @else
                    <span class="group-hover:opacity-0 fas fa-sync fa-spin text-green-500"></span>
                    <span title="{{ __mc('Pause Automation') }}"
                          class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100">
                        <x-mailcoach::rounded-icon class="w-5 h-5" type="warning" icon="fas fa-pause"/>
                    </span>
                @endif
            </button>
        </div>
    </td>
    <td class="markup-links">
        <a class="break-words" href="{{ route('mailcoach.automations.settings', $automation) }}">
            {{ $automation->name }}
        </a>
    </td>
    <td class="td-numeric">{{ $automation->updated_at->toMailcoachFormat() }}</td>
    <td class="td-action">
        <x-mailcoach::dropdown direction="left">
            <ul>
                <li>
                    <button wire:click.prevent="duplicateAutomation({{ $automation->id }})">
                        <x-mailcoach::icon-label icon="fas fa-random" :text="__mc('Duplicate')"/>
                    </button>
                </li>
                <li>
                    <x-mailcoach::confirm-button
                            :confirm-text="__mc('Are you sure you want to delete automation :automation?', ['automation' => $automation->name])"
                            onConfirm="() => $wire.deleteAutomation({{ $automation->id }})"
                    >
                        <x-mailcoach::icon-label icon="far fa-trash-alt" :text="__mc('Delete')"
                                                 :caution="true"/>
                    </x-mailcoach::confirm-button>
                </li>
            </ul>
        </x-mailcoach::dropdown>
    </td>
</tr>
