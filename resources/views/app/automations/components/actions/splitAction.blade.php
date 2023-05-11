<x-mailcoach::fieldset card class="md:p-6" :focus="$editing">
    <x-slot name="legend">
        <header class="flex items-center space-x-2">
            <span class="w-6 h-6 rounded-full inline-flex items-center justify-center text-xs leading-none font-semibold counter-automation">
                {{ $index + 1 }}
            </span>
            <span class="font-normal whitespace-nowrap">
                {{ __mc('Branch out') }}
            </span>
        </header>
    </x-slot>

    <div class="flex items-center absolute top-4 right-6 gap-4 z-10">
        @if ($editing && count($editingActions) === 0)
            <button type="button" wire:click="save" class="hover:text-green-500">
                <i class="icon-button fas fa-check"></i>
                Save
            </button>
        @elseif ($editable && !$editing)
            <button type="button" wire:click="edit">
                <i class="icon-button far fa-edit"></i>
            </button>
        @endif
        @if ($deletable && count($editingActions) === 0)
            <button type="button" onclick="confirm('{{ __mc('Are you sure you want to delete this action?') }}') || event.stopImmediatePropagation()" wire:click="delete">
                <i class="icon-button link-danger far fa-trash-alt"></i>
            </button>
        @endif
    </div>

        <div class="grid gap-6">
            @if ($editing)
                <div class="grid gap-6 w-full">
                    <section class="border-t border-r border-b border-indigo-700/10 rounded bg-indigo-300/10 before:content-[''] before:absolute before:w-2 before:-top-px before:-bottom-px before:left-0 before:bg-blue-600 before:rounded-l ">
                        <div x-data="{ collapsed: false }" :class="{ 'pb-6': !collapsed }" class="grid gap-4 px-6">
                            <div class="flex items-center">
                                <h2 :class="{ 'rounded-br': !collapsed }" class="justify-self-start -ml-4 -my-px h-8 pl-2 pr-4 inline-flex items-center bg-blue-600 text-white space-x-2">
                                    <span class="markup-h4 whitespace-nowrap overflow-ellipsis max-w-xs truncate">
                                    <span class="font-normal">{{ __mc('Branch') }}</span>
                                    A
                                </span>
                                </h2>
                                <span x-show="collapsed" class="text-gray-600 text-sm ml-4">{{ count($leftActions) }} {{ __mc_choice('action|actions', count($leftActions)) }}</span>
                                <button class="ml-auto -mr-3 text-sm" type="button">
                                    <i x-show="!collapsed" @click="collapsed = true" class="fas fa-chevron-up"></i>
                                    <i x-show="collapsed" @click="collapsed = false" class="fas fa-chevron-down"></i>
                                </button>
                            </div>
                            <div x-show="!collapsed">
                                <livewire:mailcoach::automation-builder name="{{ $uuid }}-left-actions" :automation="$automation" :actions="$leftActions" key="{{ $uuid }}-left-actions" />
                            </div>
                        </div>
                    </section>
                    <section class="border-t border-r border-b border-indigo-700/10 rounded bg-indigo-300/10 before:content-[''] before:absolute before:w-2 before:-top-px before:-bottom-px before:left-0 before:bg-blue-600 before:rounded-l ">
                        <div x-data="{ collapsed: false }" :class="{ 'pb-6': !collapsed }" class="grid gap-4 px-6">
                            <div class="flex items-center">
                                <h2 :class="{ 'rounded-br': !collapsed }" class="justify-self-start -ml-4 -my-px h-8 pl-2 pr-4 inline-flex items-center bg-blue-600 text-white space-x-2">
                                    <span class="markup-h4 whitespace-nowrap overflow-ellipsis max-w-xs truncate">
                                    <span class="font-normal">{{ __mc('Branch') }}</span>
                                    B
                                </span>
                                </h2>
                                <span x-show="collapsed" class="text-gray-600 text-sm ml-4">{{ count($rightActions) }} {{ __mc_choice('action|actions', count($rightActions)) }}</span>
                                <button class="ml-auto -mr-3 text-sm" type="button">
                                    <i x-show="!collapsed" @click="collapsed = true" class="fas fa-chevron-up"></i>
                                    <i x-show="collapsed" @click="collapsed = false" class="fas fa-chevron-down"></i>
                                </button>
                            </div>
                            <div x-show="!collapsed">
                                <livewire:mailcoach::automation-builder name="{{ $uuid }}-right-actions" :automation="$automation" :actions="$rightActions" key="{{ $uuid}}-right-actions" />
                            </div>
                        </div>
                    </section>
                </div>
            @else
                <div class="grid gap-6 flex-grow">
                    <div class="grid gap-6 w-full">
                        <section class="border-t border-r border-b border-indigo-700/10 rounded bg-indigo-300/10 before:content-[''] before:absolute before:w-2 before:-top-px before:-bottom-px before:left-0 before:bg-blue-600 before:rounded-l ">
                            <div x-data="{ collapsed: false }" :class="{ 'pb-6': !collapsed }" class="grid gap-4 px-6">
                                <div class="flex items-center">
                                    <h2 :class="{ 'rounded-br': !collapsed }" class="justify-self-start -ml-4 -my-px h-8 pl-2 pr-4 inline-flex items-center bg-blue-600 text-white space-x-2">
                                        <span class="markup-h4 whitespace-nowrap overflow-ellipsis max-w-xs truncate">
                                            <span class="font-normal">{{ __mc('Branch') }}</span>
                                            A
                                        </span>
                                    </h2>
                                    <span x-show="collapsed" class="text-gray-600 text-sm ml-4">{{ count($leftActions) }} {{ __mc_choice('action|actions', count($leftActions)) }}</span>
                                    <button class="ml-auto -mr-3 text-sm" type="button">
                                        <i x-show="!collapsed" @click="collapsed = true" class="fas fa-chevron-up"></i>
                                        <i x-show="collapsed" @click="collapsed = false" class="fas fa-chevron-down"></i>
                                    </button>
                                </div>
                                <div class="grid gap-3" x-show="!collapsed">
                                    @foreach ($leftActions as $index => $action)
                                        @livewire($action['class']::getComponent() ?: 'mailcoach::automation-action', array_merge([
                                            'index' => $index,
                                            'uuid' => $action['uuid'],
                                            'action' => $action,
                                            'automation' => $automation,
                                            'editable' => false,
                                            'deletable' => false,
                                        ], ($action['data'] ?? [])), key('left' . $index . $action['uuid']))
                                    @endforeach
                                </div>
                            </div>
                        </section>
                        <section class="border-t border-r border-b border-indigo-700/10 rounded bg-indigo-300/10 before:content-[''] before:absolute before:w-2 before:-top-px before:-bottom-px before:left-0 before:bg-blue-600 before:rounded-l ">
                            <div x-data="{ collapsed: false }" :class="{ 'pb-6': !collapsed }" class="grid gap-4 px-6">
                                <div class="flex items-center">
                                    <h2 :class="{ 'rounded-br': !collapsed }" class="justify-self-start -ml-4 -my-px h-8 pl-2 pr-4 inline-flex items-center bg-blue-600 text-white space-x-2">
                                        <span class="markup-h4 whitespace-nowrap overflow-ellipsis max-w-xs truncate">
                                        <span class="font-normal">{{ __mc('Branch') }}</span>
                                        B
                                    </span>
                                    </h2>
                                    <span x-show="collapsed" class="text-gray-600 text-sm ml-4">{{ count($rightActions) }} {{ __mc_choice('action|actions', count($rightActions)) }}</span>
                                    <button class="ml-auto -mr-3 text-sm" type="button">
                                        <i x-show="!collapsed" @click="collapsed = true" class="fas fa-chevron-up"></i>
                                        <i x-show="collapsed" @click="collapsed = false" class="fas fa-chevron-down"></i>
                                    </button>
                                </div>
                                <div class="grid gap-3" x-show="!collapsed">
                                    @foreach ($rightActions as $index => $action)
                                        @livewire($action['class']::getComponent() ?: 'mailcoach::automation-action', array_merge([
                                            'index' => $index,
                                            'uuid' => $action['uuid'],
                                            'action' => $action,
                                            'automation' => $automation,
                                            'editable' => false,
                                            'deletable' => false,
                                        ], ($action['data'] ?? [])), key('right' . $index . $action['uuid']))
                                    @endforeach
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            @endif
        </div>
</x-mailcoach::fieldset>

