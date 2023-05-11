<div>
    <x-mailcoach::warning>
        <p>{{ __mc('The email builder stores content in a structured way. When switching from or to this builder, content in existing draft campaigns might get lost.') }}</p>
    </x-mailcoach::warning>

    <x-mailcoach::info class="mt-6">
        <p>{!! __mc('Our email builder is powered by <a href=":link" target="_blank">Unlayer</a>, a beautiful editor that allows you to edit html in a structured way. You don\'t need any HTML knowledge to compose a campaign.', ['link' => 'https://unlayer.com']) !!}</p>
    </x-mailcoach::info>

    <x-mailcoach::fieldset>
        <div>
            <x-mailcoach::text-field
                :label="__mc('Unlayer Project ID')"
                name="editorSettings.project_id"
                wire:model.lazy="editorSettings.project_id"
                type="text"
            />
            <x-mailcoach::info class="mt-1">
                {{ __mc('If you have a paid Unlayer account, you can enter your project ID here') }}
            </x-mailcoach::info>
        </div>
    </x-mailcoach::fieldset>



</div>
