<x-mailcoach::fieldset card class="md:p-6" :focus="$editing">
    <x-slot name="legend">
        <header class="flex items-center space-x-2">
            <span class="w-6 h-6 rounded-full inline-flex items-center justify-center text-xs leading-none font-semibold counter-automation">
                {{ $index + 1 }}
            </span>
            <span class="font-normal whitespace-nowrap">
                Check for
                <span class="form-legend-accent">
                    {{ \Carbon\CarbonInterval::createFromDateString("{$length} {$unit}") }}
                </span>
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
                <div class="form-grid">
                    <div class="form-actions">
                        <div class="col-span-8 sm:col-span-4">
                            <x-mailcoach::text-field
                                :label="__mc('Duration')"
                                :required="true"
                                name="length"
                                wire:model="length"
                                type="number"
                            />
                        </div>
                        <div class="col-span-4 sm:col-span-4">
                            <x-mailcoach::select-field
                                :label="__mc('Unit')"
                                :required="true"
                                name="unit"
                                wire:model="unit"
                                :options="
                                    collect($units)
                                        ->mapWithKeys(fn ($label, $value) => [$value => \Illuminate\Support\Str::plural($label, (int) $length)])
                                        ->toArray()
                                "
                            />
                        </div>

                        <div class="col-span-12 sm:col-span-4 sm:col-start-1">
                            <x-mailcoach::select-field
                                :label="__mc('Condition')"
                                :required="true"
                                name="condition"
                                wire:model="condition"
                                :placeholder="__mc('Select a condition')"
                                :options="$conditionOptions"
                            />
                        </div>

                        @switch ($condition)
                            @case (\Spatie\Mailcoach\Domain\Automation\Support\Conditions\HasTagCondition::class)
                                <div class="col-span-12 sm:col-span-4">
                                    <x-mailcoach::tags-field
                                        :label="__mc('Tag')"
                                        name="conditionData.tag"
                                        wire:model="conditionData.tag"
                                        :multiple="false"
                                        :clearable="false"
                                        :tags="$automation->emailList?->tags()->pluck('name')->toArray() ?? []"
                                    />
                                </div>
                            @break
                            @case (\Spatie\Mailcoach\Domain\Automation\Support\Conditions\HasOpenedAutomationMail::class)
                                <div class="col-span-12 sm:col-span-4">
                                    <x-mailcoach::select-field
                                        :label="__mc('Automation mail')"
                                        name="conditionData.automation_mail_id"
                                        wire:model="conditionData.automation_mail_id"
                                        :placeholder="__mc('Select a mail')"
                                        :options="
                                            \Spatie\Mailcoach\Mailcoach::getAutomationMailClass()::query()->orderBy('name')->pluck('name', 'id')
                                        "
                                    />
                                </div>
                            @break
                            @case (\Spatie\Mailcoach\Domain\Automation\Support\Conditions\HasClickedAutomationMail::class)
                                <div class="col-span-12 sm:col-span-4">
                                    <x-mailcoach::select-field
                                        :label="__mc('Automation mail')"
                                        name="conditionData.automation_mail_id"
                                        wire:model="conditionData.automation_mail_id"
                                        :placeholder="__mc('Select a mail')"
                                        :required="true"
                                        :options="
                                            \Spatie\Mailcoach\Mailcoach::getAutomationMailClass()::query()
                                                ->orderBy('name')
                                                ->pluck('name', 'id')
                                        "
                                    />
                                </div>

                                @if ($conditionData['automation_mail_id'])
                                    <div class="col-span-12 sm:col-span-4">
                                        <x-mailcoach::select-field
                                            :label="__mc('Link')"
                                            name="conditionData.automation_mail_link_url"
                                            wire:model="conditionData.automation_mail_link_url"
                                            :placeholder="__mc('Select a link')"
                                            :required="false"
                                            :options="
                                                ['' => __mc('Any link')] +
                                                \Spatie\Mailcoach\Mailcoach::getAutomationMailClass()::find($conditionData['automation_mail_id'])
                                                    ->htmlLinks()
                                                    ->mapWithKeys(fn ($url) => [$url => $url])
                                                    ->toArray()
                                            "
                                        />
                                    </div>
                                @endif
                            @break
                        @endswitch
                    </div>
                </div>

                <div class="grid gap-6 w-full">
                    <section class="border-t border-r border-b border-indigo-700/10 rounded bg-indigo-300/10 before:content-[''] before:absolute before:w-2 before:-top-px before:-bottom-px before:left-0 before:bg-green-600 before:rounded-l ">
                        <div x-data="{ collapsed: false }" :class="{ 'pb-6': !collapsed }" class="grid gap-4 px-6">
                            <div class="flex items-center">
                                <h2 :class="{ 'rounded-br': !collapsed }" class="justify-self-start -ml-4 -my-px h-8 pl-2 pr-4 inline-flex items-center bg-green-600 text-white space-x-2">
                                    <i class="far fa-thumbs-up"></i>
                                    <span class="markup-h4">@lang('If')</span>
                                </h2>
                                <span x-show="collapsed" class="text-gray-600 text-sm ml-4">{{ count($yesActions) }} {{ __mc_choice('action|actions', count($yesActions)) }}</span>
                                <button class="ml-auto -mr-3 text-sm" type="button">
                                    <i x-show="!collapsed" @click="collapsed = true" class="fas fa-chevron-up"></i>
                                    <i x-show="collapsed" @click="collapsed = false" class="fas fa-chevron-down"></i>
                                </button>
                            </div>
                            <div x-show="!collapsed">
                                <livewire:mailcoach::automation-builder name="{{ $uuid }}-yes-actions" :automation="$automation" :actions="$yesActions" key="{{ $uuid }}-yes-actions" />
                            </div>
                        </div>
                    </section>
                    <section class="border-t border-r border-b border-indigo-700/10 rounded bg-indigo-300/10 before:content-[''] before:absolute before:w-2 before:-top-px before:-bottom-px before:left-0 before:bg-red-600 before:rounded-l ">
                        <div x-data="{ collapsed: false }" :class="{ 'pb-6': !collapsed }" class="grid gap-4 px-6">
                            <div class="flex items-center">
                                <h2 :class="{ 'rounded-br': !collapsed }" class="justify-self-start -ml-4 -my-px h-8 pl-2 pr-4 inline-flex items-center bg-red-600 text-white space-x-2">
                                    <i class="far fa-thumbs-down"></i>
                                    <span class="markup-h4">@lang('Else')</span>
                                </h2>
                                <span x-show="collapsed" class="text-gray-600 text-sm ml-4">{{ count($noActions) }} {{ __mc_choice('action|actions', count($noActions)) }}</span>
                                <button class="ml-auto -mr-3 text-sm" type="button">
                                    <i x-show="!collapsed" @click="collapsed = true" class="fas fa-chevron-up"></i>
                                    <i x-show="collapsed" @click="collapsed = false" class="fas fa-chevron-down"></i>
                                </button>
                            </div>
                            <div x-show="!collapsed">
                                <livewire:mailcoach::automation-builder name="{{ $uuid }}-no-actions" :automation="$automation" :actions="$noActions" key="{{ $uuid}}-no-actions" />
                            </div>
                        </div>
                    </section>
                </div>
            @else
                <div class="grid gap-6 flex-grow">
                    <div class="grid gap-6 w-full">
                        <section class="border-t border-r border-b border-indigo-700/10 rounded bg-indigo-300/10 before:content-[''] before:absolute before:w-2 before:-top-px before:-bottom-px before:left-0 before:bg-green-600 before:rounded-l ">
                            <div x-data="{ collapsed: false }" :class="{ 'pb-6': !collapsed }" class="grid gap-4 px-6">
                                <div class="flex items-center">
                                    <h2 :class="{ 'rounded-br': !collapsed }" class="justify-self-start -ml-4 -my-px h-8 pl-2 pr-4 inline-flex items-center bg-green-600 text-white space-x-2">
                                        <i class="far fa-thumbs-up"></i>
                                         @if ($condition)
                                            <span class="markup-h4 whitespace-nowrap overflow-ellipsis max-w-xs truncate">
                                                <span class="font-normal">@lang('If') {{ $condition::getName() }}</span>
                                                <span class="font-semibold tracking-normal normal-case">{{ $condition::getDescription($conditionData) }}</span>?
                                            </span>
                                        @endif
                                    </h2>
                                    <span x-show="collapsed" class="text-gray-600 text-sm ml-4">{{ count($yesActions) }} {{ __mc_choice('action|actions', count($yesActions)) }}</span>
                                    <button class="ml-auto -mr-3 text-sm" type="button">
                                        <i x-show="!collapsed" @click="collapsed = true" class="fas fa-chevron-up"></i>
                                        <i x-show="collapsed" @click="collapsed = false" class="fas fa-chevron-down"></i>
                                    </button>
                                </div>
                                <div class="grid gap-3" x-show="!collapsed">
                                    @foreach ($yesActions as $index => $action)
                                        @livewire($action['class']::getComponent() ?: 'mailcoach::automation-action', array_merge([
                                            'index' => $index,
                                            'uuid' => $action['uuid'],
                                            'action' => $action,
                                            'automation' => $automation,
                                            'editable' => false,
                                            'deletable' => false,
                                        ], ($action['data'] ?? [])), key('yes' . $index . $action['uuid']))
                                    @endforeach
                                </div>
                            </div>
                        </section>
                        <section class="border-t border-r border-b border-indigo-700/10 rounded bg-indigo-300/10 before:content-[''] before:absolute before:w-2 before:-top-px before:-bottom-px before:left-0 before:bg-red-600 before:rounded-l ">
                            <div x-data="{ collapsed: false }" :class="{ 'pb-6': !collapsed }" class="grid gap-4 px-6">
                                <div class="flex items-center">
                                    <h2 :class="{ 'rounded-br': !collapsed }" class="justify-self-start -ml-4 -my-px h-8 pl-2 pr-4 inline-flex items-center bg-red-600 text-white space-x-2">
                                        <i class="far fa-thumbs-down"></i>
                                        <span class="markup-h4">
                                            <span class="font-normal">@lang('Else')</span>
                                        </span>
                                    </h2>
                                    <span x-show="collapsed" class="text-gray-600 text-sm ml-4">{{ count($noActions) }} {{ __mc_choice('action|actions', count($noActions)) }}</span>
                                    <button class="ml-auto -mr-3 text-sm" type="button">
                                        <i x-show="!collapsed" @click="collapsed = true" class="fas fa-chevron-up"></i>
                                        <i x-show="collapsed" @click="collapsed = false" class="fas fa-chevron-down"></i>
                                    </button>
                                </div>
                                <div class="grid gap-3" x-show="!collapsed">
                                    @foreach ($noActions as $index => $action)
                                        @livewire($action['class']::getComponent() ?: 'mailcoach::automation-action', array_merge([
                                            'index' => $index,
                                            'uuid' => $action['uuid'],
                                            'action' => $action,
                                            'automation' => $automation,
                                            'editable' => false,
                                            'deletable' => false,
                                        ], ($action['data'] ?? [])), key('no' . $index . $action['uuid']))
                                    @endforeach
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            @endif
        </div>
</x-mailcoach::fieldset>

