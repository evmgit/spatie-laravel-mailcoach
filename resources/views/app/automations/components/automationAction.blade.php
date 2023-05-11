<x-mailcoach::fieldset card class="md:p-6" :focus="$editing">
    <x-slot name="legend">
        <header class="flex items-center space-x-2">
            <span class="w-6 h-6 rounded-full inline-flex items-center justify-center text-xs leading-none font-semibold counter-automation">
                {{ $index + 1 }}
            </span>
            <span class="font-normal">
                {{ $legend ?? $action['class']::getName() }}
            </span>
        </header>
    </x-slot>

    <div class="flex items-center absolute top-4 right-6 gap-4 z-10">
        @if ($editing)
            <button type="button" wire:click="save" class="hover:text-green-500">
                <i class="icon-button fas fa-check"></i>
                Save
            </button>
        @elseif ($editable)
            <button type="button" wire:click="edit">
                <i class="icon-button far fa-edit"></i>
            </button>
        @endif
        @if ($deletable)
            <button type="button" onclick="confirm('{{ __mc('Are you sure you want to delete this action?') }}') || event.stopImmediatePropagation()" wire:click="delete">
                <i class="icon-button link-danger far fa-trash-alt"></i>
            </button>
        @endif
    </div>

    @if ($editing)
        <div class="form-actions">
            {{ $form ?? '' }}
        </div>
    @else
        @if($content ?? false)
            <div>
                {{ $content }}
            </div>
        @endif

        <dl class="-mb-6 -mx-6 px-6 py-2 flex items-center justify-end text-xs bg-indigo-300/10 border-t border-indigo-700/10">
            <span>
                Active
                <span class="font-semibold variant-numeric-tabular">{{ number_format($action['active'] ?? 0) }}</span>
            </span>
            <span class="text-gray-400 px-2">•</span>
            <span>
                Completed
                <span class="font-semibold variant-numeric-tabular">{{ number_format($action['completed'] ?? 0) }}</span>
            </span>
            <span>
                <span class="text-gray-400 px-2">•</span>
                Halted
                <span class="font-semibold variant-numeric-tabular">{{ number_format($action['halted'] ?? 0) }}</span>
            </span>
        </dl>
    @endif
</x-mailcoach::fieldset>
