<form
    method="POST"
    action="{{ $action }}"
    {{ $attributes->except('class') }}
    @isset($dataConfirm) x-on:click="$store.modals.open('confirm', '{{ $dataConfirmText ?? '' }}')" @endisset
>
    @csrf
    @method($method ?? 'POST')
        <button
            type="submit"
            class="{{ $class ?? '' }}"
        >
            {{ $slot }}
        </button>
</form>
