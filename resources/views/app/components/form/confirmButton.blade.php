@props([
    'confirmText' => __mc('Are you sure?'),
    'onConfirm' => '() => $refs.form.submit()',
    'action' => null,
    'method' => 'POST',
    'class' => '',
    'disabled' => false,
])
<form
    method="POST"
    action="{{ $action }}"
    {{ $attributes->except('class') }}
    x-data
    x-ref="form"
>
    @csrf
    @method($method)
    <button
        x-on:click.prevent="
            $store.modals.open('confirm');
            confirmText = @js($confirmText);
            onConfirm = {{ $onConfirm }};
        "
        type="submit"
        class="{{ $class ?? '' }}"
        @if($disabled) disabled @endif
    >
        {{ $slot }}
    </button>
</form>
