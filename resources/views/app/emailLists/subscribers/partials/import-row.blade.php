<tr>
    <td>
        @switch($row->status)
            @case(\Spatie\Mailcoach\Domain\Audience\Enums\SubscriberImportStatus::Pending)
                <i title="{{ __mc('Scheduled') }}" class="far fa-clock text-orange-500`"></i>
                @break
            @case(\Spatie\Mailcoach\Domain\Audience\Enums\SubscriberImportStatus::Importing)
                <i title="{{ __mc('Importing') }}"
                   class="fas fa-sync fa-spin text-blue-500"></i>
                @break
            @case(\Spatie\Mailcoach\Domain\Audience\Enums\SubscriberImportStatus::Completed)
                <i title="{{ __mc('Completed') }}" class="fas fa-check text-green-500"></i>
                @break
            @case(\Spatie\Mailcoach\Domain\Audience\Enums\SubscriberImportStatus::Failed)
                <i title="{{ __mc('Failed') }}" class="fas fa-times text-red-500"></i>
            @break
        @endswitch
    </td>
    <td class="td-numeric">
        {{ $row->created_at->toMailcoachFormat() }}
    </td>
    <td class="td-numeric">{{ number_format($row->imported_subscribers_count) }}</td>
    <td class="td-numeric">{{ count($row->errors ?? []) }}</td>
    <td class="td-action">
        <x-mailcoach::dropdown direction="left">
            <ul>
                @if (count($row->errors ?? []))
                    <li>
                        <a href="#"
                           wire:click.prevent="downloadAttatchment('{{ $row->id }}', 'errorReport')"
                           download>
                            <x-mailcoach::icon-label icon="far fa-times-circle"
                                                     :text="__mc('Error report')"/>
                        </a>
                    </li>
                @endif
                <li>
                    <a href="#"
                       wire:click.prevent="downloadAttatchment('{{ $row->id }}', 'importFile')"
                       download>
                        <x-mailcoach::icon-label icon="far fa-file"
                                                 :text="__mc('Uploaded file')"/>
                    </a>
                </li>
                <li>
                    <x-mailcoach::confirm-button
                        :confirm-text="__mc('Are you sure you want to restart this import?')"
                        onConfirm="() => $wire.restartImport({{ $row->id }})"
                    >
                        <x-mailcoach::icon-label icon="fa-fw far fa-sync" :text="__mc('Restart')"/>
                    </x-mailcoach::confirm-button>
                </li>
                <li>
                    <x-mailcoach::confirm-button
                        :confirm-text="__mc('Are you sure you want to delete this import? Don\'t worry, it only deletes the import record and not subscribers.')"
                        onConfirm="() => $wire.deleteImport({{ $row->id }})"
                    >
                        <x-mailcoach::icon-label icon="fa-fw far fa-trash-alt" :text="__mc('Delete')"
                                                 :caution="true"/>
                    </x-mailcoach::confirm-button>
                </li>
            </ul>
        </x-mailcoach::dropdown>
    </td>
</tr>
