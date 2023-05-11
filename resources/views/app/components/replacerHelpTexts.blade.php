@if ($replacerHelpTexts())
    <div>
        <button class="link-dimmed" x-on:click.prevent="$store.modals.open('placeholders')">
            {{__mc('Placeholder cheat sheet')}}
        </button>
    </div>

    <x-mailcoach::modal medium :dismissable="true" :title="__mc('Placeholder cheat sheet')" name="placeholders">
        <x-mailcoach::info class="markup-code">
            {{ __mc('You can use following placeholders in the subject and copy:') }}
        </x-mailcoach::info>
            <dl class="mt-4 markup-dl markup-code">
                @foreach($replacerHelpTexts as $replacerName => $replacerDescription)
                    <dt x-data="{ value: @js("::{$replacerName}::") }"><code @click="() => {
                        $clipboard(value);
                        value = '{{ __mc('Copied!') }}';
                        setTimeout(() => {
                            value = '::{{ $replacerName }}::';
                        }, 2000);
                    }" x-text="value"></code></dt>
                    <dd>{{ $replacerDescription }}</dd>
                @endforeach
            </dl>
    </x-mailcoach::modal>
@endif
