@props([
    'label' => '',
    'required' => false,
    'name' => '',
    'multiple' => true,
    'tags' => [],
    'value' => [],
    'allowCreate' => false,
    'clearable' => true,
])

@php($wireModelAttribute = collect($attributes)->first(fn ($value, $attribute) => str_starts_with($attribute, 'wire:model')))
<div
    wire:ignore
    x-data="{
        multiple: {{ $multiple ? 'true' : 'false' }},
        @if ($wireModelAttribute)
        value: @entangle($wireModelAttribute),
        @else
        value: @js($value),
        @endif
        options: @js(array_values($tags)),
        init() {
            this.$nextTick(() => {
                const self = this;
                function createChoicesFromTags() {
                    let selection = self.multiple ? self.value : [self.value]

                    return self.options.map(tag => {
                        return {
                            value: tag,
                            label: tag,
                            selected: selection.includes(tag),
                            customProperties: {
                                isCurrentSearch: false,
                                exists: true,
                            },
                        };
                    });
                }

                let tagsInput = new Choices(this.$refs.select, {
                    removeItemButton: {{ $clearable ? 'true' : 'false' }},
                    allowHTML: true,
                    searchEnabled: this.options.length >= 10,
                    searchResultLimit: 10,
                    searchPlaceholderValue: '{{ __mc('Search…') }}',
                    noResultsText: '{{ $allowCreate ? __mc('Type to add tags') : __mc('No tags found') }}',
                    noChoicesText: '{{ $allowCreate ? __mc('Type to add tags') : __mc('No tags to choose from') }}',
                    itemSelectText: '{{ $allowCreate ? __mc('Press to add') : __mc('Press to select') }}',
                    choices: createChoicesFromTags(),
                });

                function updateChoices(search) {
                    const hasCurrentSearchCoice = Boolean(
                        tagsInput._currentState.choices.find(choice => choice.customProperties.isCurrentSearch)
                    );

                    if (!hasCurrentSearchCoice && !search) return;

                    if (!hasCurrentSearchCoice) {
                        addCurrentSearchChoice(search);
                        return;
                    }

                    if (!search) {
                        removeCurrentSearchChoice();
                        return;
                    }

                    updateCurrentSearchChoice(search);
                }

                function addCurrentSearchChoice(search) {
                    if (!hasExistingTag(search)) {
                        tagsInput.setChoices([
                            {
                                value: search,
                                label: search,
                                customProperties: {
                                    isCurrentSearch: true,
                                    exists: false,
                                },
                            },
                        ]);
                    }
                }

                function updateCurrentSearchChoice(search) {
                    if (hasExistingTag(search)) {
                        removeCurrentSearchChoice();
                    } else {
                        tagsInput._currentState.choices.forEach(choice => {
                            if (choice.customProperties.isCurrentSearch) {
                                choice.value = search;
                                choice.label = search;
                            }
                        });
                    }
                }

                function removeCurrentSearchChoice() {
                    const currentSearchChoiceIndex = tagsInput._currentState.choices.findIndex(
                        choice => choice.customProperties.isCurrentSearch
                    );

                    if (currentSearchChoiceIndex !== -1) {
                        tagsInput._currentState.choices.splice(currentSearchChoiceIndex, 1);
                    }
                }

                function hasExistingTag(value) {
                    return (
                        tagsInput._currentState.choices.findIndex(choice => {
                            if (choice.customProperties.isCurrentSearch) {
                                return false;
                            }

                            return choice.value.toLowerCase() === value.toLowerCase();
                        }) !== -1
                    );
                }

                this.$refs.select.addEventListener('addItem', () => {
                    tagsInput._currentState.choices.forEach(choice => {
                        delete choice.customProperties.isCurrentSearch;
                    });
                });

                tagsInput.input.element.addEventListener('blur', function() {
                    if (tagsInput.input.value) {
                        tagsInput.setValue([tagsInput.input.value]);
                        tagsInput.clearInput();

                        $wire.emit('tags-updated', tagsInput.getValue(true));
                        $wire.emit('tags-updated-{{ $name }}', tagsInput.getValue(true));
                    }
                });

                @if ($multiple && $allowCreate)
                    document.querySelector('input.choices__input', this.$refs.select.parentNode).addEventListener('input', event => {
                        updateChoices(event.target.value);
                    });
                @endif

                let refreshChoices = () => {
                    tagsInput.clearStore()
                    tagsInput.setChoices(createChoicesFromTags())
                }

                refreshChoices();

                this.$refs.select.addEventListener('change', () => {
                    this.value = tagsInput.getValue(true)
                    $wire.emit('tags-updated', this.value);
                    $wire.emit('tags-updated-{{ $name }}', this.value);
                })
            })
        }
    }"
    class="form-field choices-multiple"
>
    @isset($label)
        <label class="{{ $required ? 'label label-required' : 'label' }}" for="{{ $name }}">
            {{ $label }}

            @if ($help ?? null)
                <i class="ml-1 text-purple-500 opacity-75 cursor-pointer fas fa-question-circle" x-data x-tooltip="{{ $help }}"></i>
            @endif
        </label>
    @endisset
    <select
        x-ref="select"
        id="{{ $name }}"
        {{ $required ? 'required' : '' }}
        {{ $multiple ? 'multiple' : '' }}
        {!! $attributes->except(['value', 'tags', 'required', 'multiple', 'name', 'allowCreate']) ?? '' !!}
        class="hidden"
    ></select>
    @if (! $multiple)
        <div class="select-arrow mt-3 pt-[3px]">
            <i class="fas fa-angle-down"></i>
        </div>
    @endif
    <template x-for="tag in value">
        <input type="hidden" name="{{ $name }}[]" :value="tag">
    </template>
    @error($name)
        <p class="form-error" role="alert">{{ $message }}</p>
    @enderror
</div>
