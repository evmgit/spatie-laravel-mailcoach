<x-mailcoach::fieldset card :legend="__mc('Usage in Mailcoach API')">
    <div>
        <x-mailcoach::help>
            {!! __mc('Whenever you need to specify a <code>:resourceName</code> in the Mailcoach API and want to use this :resource, you\'ll need to pass this value', [
            'resourceName' => 'mailer',
            'resource' => 'mailer',
        ]) !!}
            <p class="mt-4">
                <x-mailcoach::code-copy class="flex items-center justify-between max-w-md" :code="$mailer->configName()"></x-mailcoach::code-copy>
            </p>
        </x-mailcoach::help>
    </div>
</x-mailcoach::fieldset>
