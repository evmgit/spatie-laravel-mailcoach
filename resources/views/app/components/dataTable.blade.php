@props([
'rows' => collect(),
'totalRowsCount' => 0,
'name' => '',
'columns' => [],
'filters' => [],
'rowPartial' => null,
'rowData' => [],
'modelClass' => null,
'emptyText' => null,
'createText' => null,
'noResultsText' => null,
'searchable' => true,
'selectable' => false,
'bulkActions' => [],
])
<div wire:init="loadRows" class="card-grid">
    @if (isset($actions) || $modelClass || count($filters) || $searchable)
        <div class="table-actions">
            {{ $actions ?? '' }}
            @if ($modelClass)
                @can('create', $modelClass)
                    <x-mailcoach::button x-on:click="$store.modals.open('create-{{ $name }}')"
                                         :label="$createText ?? __mc('Create ' . $name)"/>
                    <x-mailcoach::modal :title="$createText ?? __mc('Create ' . $name)" name="create-{{ $name }}">
                        @livewire('mailcoach::create-' . $name)
                    </x-mailcoach::modal>
                @endcan
            @endif

            <div class="table-filters">
                @if (count($filters))
                    <x-mailcoach::filters>
                        @foreach ($filters as $filter)
                            @php($attribute = $filter['attribute'])
                            <x-mailcoach::filter :current="$this->$attribute"
                                                 value="{{ $filter['value'] instanceof UnitEnum ? $filter['value']->value : $filter['value'] }}"
                                                 attribute="{{ $filter['attribute'] }}">
                                {{ $filter['label'] }}
                                <span
                                    class="counter">{{ Illuminate\Support\Str::shortNumber($filter['count'] ?? 0) }}</span>
                            </x-mailcoach::filter>
                        @endforeach
                    </x-mailcoach::filters>
                @endif

                @if($searchable)
                    <x-mailcoach::search wire:model.debounce.500ms="search" :placeholder="__mc('Search…')"/>
                @endif
            </div>
        </div>
    @endif

    <div class="card p-0 pb-24 md:pb-0 overflow-x-auto md:overflow-visible">
        <div wire:loading.delay.longer wire:target="loadRows">
            <table class="table-styled w-full">
                <thead>
                <tr>
                    @foreach ($columns as $column)
                        <x-mailcoach::th :class="$column['class'] ?? ''" :sort="$this->sort"
                                         :property="$column['attribute'] ?? null">
                            {{ $column['label'] ?? '' }}
                        </x-mailcoach::th>
                    @endforeach
                </tr>
                </thead>
                <tbody>
                @foreach (range(1, 5) as $i)
                    <tr class="markup-links">
                        @foreach ($columns as $column)
                            @if ($loop->last)
                                <td class="td-action"></td>
                            @else
                                <td class="{{ $column['class'] ?? '' }}">
                                    <div class="animate-pulse h-4 my-1 bg-gradient-to-r from-indigo-900/5"></div>
                                </td>
                            @endif
                        @endforeach
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div wire:loading.delay.longer.remove wire:target="loadRows">
            <table class="table-styled w-full">
                <thead>
                <tr>
                    @if ($selectable)
                        <x-mailcoach::th class="w-4">
                            <x-mailcoach::checkbox-field type="checkbox" name="selectAll" label="" :checked="$this->selectedAll" wire:change="selectAll" />
                        </x-mailcoach::th>
                    @endif
                    @foreach ($columns as $column)
                        <x-mailcoach::th :class="$column['class'] ?? ''" :sort="$this->sort"
                                         :property="$column['attribute'] ?? null">
                            {{ $column['label'] ?? '' }}
                        </x-mailcoach::th>
                    @endforeach
                </tr>
                </thead>
                <tbody>
                @if ($selectable && $rows->count() && count($this->selectedRows))
                    <tr>
                        <td colspan="{{ count($columns) + 1 }}" class="bg-gradient-to-r from-orange-50/60 to-orange-50/40">
                            <div class="flex justify-between items-center">
                                <x-mailcoach::info>
                                    Selected {{ count($this->selectedRows) }} {{ __mc_choice('row|rows', count($this->selectedRows)) }}@if($this->selectedAll && count($this->selectedRows) < $rows->total()), <a href="#" wire:click="selectAll(true)">select all {{ $rows->total() }} rows</a>@endif
                                </x-mailcoach::info>

                                @if (count($bulkActions))
                                    <div class="flex items-center text-sm">
                                        <div class="w-56">
                                            <div class="flex items-center">
                                                <div class="select mr-1">
                                                    <select class="!rounded-lg" wire:model="bulkAction">
                                                        <option value="" disabled>{{ __mc('Select an action') }}</option>
                                                        @foreach ($bulkActions as $action)
                                                            <option value="{{ $action['method'] }}">
                                                                {{ trans_choice($action['label'], count($this->selectedRows), ['count' => count($this->selectedRows)]) }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <div class="select-arrow">
                                                        <i class="fas fa-angle-down"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <x-mailcoach::confirm-button class="button" :disabled="!$this->bulkAction" on-confirm="() => $wire.{{ $this->bulkAction }}()">
                                            {{ __mc('Apply') }}
                                        </x-mailcoach::confirm-button>
                                    </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endif
                @if ($rows->count())
                    @if($rowPartial)
                        @foreach ($rows as $index => $row)
                            @include($rowPartial, $rowData)
                        @endforeach
                    @endif
                    {{ $tbody ?? '' }}
                @endif
                </tbody>
            </table>
        </div>

        @if(!$rows->count() && $this->readyToLoad)
            <div class="p-6 md:px-10">
                @if(isset($empty))
                    {{ $empty }}
                @else
                    @php($plural = \Illuminate\Support\Str::plural($name))
                    @if ($this->search ?? null)
                        <x-mailcoach::info>
                            {{ $noResultsText ?? __mc("No {$plural} found.") }}
                        </x-mailcoach::info>
                    @else
                        <x-mailcoach::info>
                            {!! $emptyText ?? __mc("No {$plural}.") !!}
                        </x-mailcoach::info>
                    @endif
                @endif
            </div>
        @endif
    </div>


    @if ($rows->count())
        <div class="flex items-center">
            @if($rows->lastPage() > 1)
                <div class="flex items-center">
                    <span class="text-sm">{{ __mc('Show') }}</span>
                    <div class="select ml-2 mr-4 w-[4.35rem]">
                        <select wire:model="perPage">
                            <option>15</option>
                            <option>25</option>
                            <option>50</option>
                            <option>100</option>
                        </select>
                        <div class="select-arrow">
                            <i class="fas fa-angle-down"></i>
                        </div>
                    </div>
                </div>
            @endif
            <div class="w-full">
                <x-mailcoach::table-status :name="__mc('' . $name)" :paginator="$rows" :total-count="$totalRowsCount"
                                           wire:click="clearFilters"></x-mailcoach::table-status>
            </div>
        </div>
    @endif

</div>
