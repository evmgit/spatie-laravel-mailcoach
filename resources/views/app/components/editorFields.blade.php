@props([
    'name',
    'label' => null,
    'type' => 'editor',
])
<div wire:key="{{ $name }}" class="form-field max-w-full" wire:key="{{ $name }}">
    <label class="label" for="field_{{ $name }}">
        {{ $label ?? \Illuminate\Support\Str::of($name)->snake(' ')->ucfirst() }}
    </label>

    @if ($type === 'text')
        <x-mailcoach::text-field
            name="templateFieldValues.{{ $name }}"
            wire:model.lazy="templateFieldValues.{{ $name }}"
            data-dirty-check
        />
    @else
        {!! $editor !!}
    @endif

    @error('templateFieldValues.' . $name)
    <p class="form-error">{{ $message }}</p>
    @enderror
</div>
