<div class="card-grid">
<x-mailcoach::card>
    <x-mailcoach::help>
        {!! __mc('You can use tokens to authenticate with the Mailcoach API. You\'ll find more info in <a href=":docsUrl" target="_blank">our docs</a>.', [
                'docsUrl' => 'https://mailcoach.app/docs'
                ]) !!}
    </x-mailcoach::help>

    <form
      wire:submit.prevent="save"
      method="POST"
    >
        @csrf

        <div class="flex items-end max-w-xl">
            <div class="flex-grow mr-2">
                <x-mailcoach::text-field
                    :label="__mc('Token name')"
                    name="name"
                    wire:model.lazy="name"
                    :placeholder="__mc('My API token')"
                    :required="true"
                    type="text"
                />
            </div>

            <x-mailcoach::button :label="__mc('Create token')"/>
        </div>

        @error('emails')
        <p class="form-error">{{ $message }}</p>
        @enderror

    </form>


    @if ($newToken)
        <x-mailcoach::help>
            <p class="mb-2">
                {{ __mc('We will display this token only once. Make sure to copy it to a safe place.') }}
            </p>

            <x-mailcoach::code-copy :code="$newToken"/>
        </x-mailcoach::help>
    @endif

</x-mailcoach::card>

@if (count($tokens))
<x-mailcoach::card class="p-0">
        <table class="table-styled">
            <thead>
            <tr>
                <x-mailcoach::th>{{ __mc('Name') }}</x-mailcoach::th>
                <x-mailcoach::th>{{ __mc('Last used at') }}</x-mailcoach::th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($tokens as $token)
                <tr>
                    <td>{{ $token->name }}</td>
                    <td>{{ $token->last_used_at ?? 'Not used yet' }}</td>
                    <td class="td-action">
                        <x-mailcoach::confirm-button :confirm-text="__mc('Are you sure you want to delete this token?')" on-confirm="() => $wire.delete({{ $token->id }})">
                            <x-mailcoach::icon-label icon="far fa-trash-alt" :caution="true"/>
                        </x-mailcoach::confirm-button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </x-mailcoach::card>
    @endif
</div>
