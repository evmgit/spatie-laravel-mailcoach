<div>
    <nav class="tabs">
        <ul>
            <x-mailcoach::navigation-item wire:click.prevent="$set('tab', 'details')" :active="$tab === 'details'">
                {{ __mc('Segment details') }}
            </x-mailcoach::navigation-item>
            <x-mailcoach::navigation-item wire:click.prevent="$set('tab', 'population')" :active="$tab === 'population'">
                <x-mailcoach::icon-label :text="__mc('Population')" invers :count="$selectedSubscribersCount" />
            </x-mailcoach::navigation-item>
        </ul>
    </nav>

    @if ($tab === 'details')
        <form
            wire:submit.prevent="save"
            @keydown.prevent.window.cmd.s="$wire.call('save')"
            @keydown.prevent.window.ctrl.s="$wire.call('save')"
            method="POST"
        >
        <x-mailcoach::card>

            @if (! $emailList->tags()->count())
                <x-mailcoach::info>
                    <div class="markup-lists">
                        {{ __mc('A segment is based on tags.') }}
                        <ol class="mt-4">
                            <li>{!! __mc('<a href=":tagLink">Create some tags</a> for this list first.', ['tagLink' => route('mailcoach.emailLists.tags', $emailList)]) !!}</li>
                            <li>{!! __mc('Assign these tags to some of the <a href=":subscriberslink">subscribers</a>.', ['subscriberslink' => route('mailcoach.emailLists.subscribers', $emailList)]) !!}</li>
                        </ol>
                    </div>
                </x-mailcoach::info>
            @endif

            @csrf
            @method('PUT')

            <x-mailcoach::text-field :label="__mc('Name')" name="segment.name" wire:model.lazy="segment.name" type="name" required />

            <div class="form-field">
                <label class=label>{{ __mc('Include with tags') }}</label>
                <div class="flex items-end">
                    <div class="flex-none">
                        <x-mailcoach::select-field
                            name="positive_tags_operator"
                            wire:model="positive_tags_operator"
                            :options="['any' => __mc('Any'), 'all' => __mc('All')]"
                        />
                    </div>
                    <div class="ml-2 flex-grow">
                        <x-mailcoach::tags-field
                            name="positive_tags"
                            :value="$positive_tags"
                            :tags="$emailList->tags()->pluck('name')->unique()->toArray()"
                        />
                    </div>
                </div>
            </div>

            <div class="form-field">
                <label class=label>{{ __mc('Exclude with tags') }}</label>
                <div class="flex items-end">
                    <div class="flex-none">
                        <x-mailcoach::select-field
                            name="negative_tags_operator"
                            wire:model="negative_tags_operator"
                            :options="['any' => __mc('Any'), 'all' => __mc('All')]"
                        />
                    </div>
                    <div class="ml-2 flex-grow">
                        <x-mailcoach::tags-field
                            name="negative_tags"
                            :value="$negative_tags"
                            :tags="$emailList->tags()->pluck('name')->unique()->toArray()"
                        />
                    </div>
                </div>
            </div>


            <x-mailcoach::form-buttons>
                <x-mailcoach::button :label="__mc('Save segment')" />
            </x-mailcoach::form-buttons>
        </x-mailcoach::card>

        <x-mailcoach::fieldset class="mt-6" card :legend="__mc('Usage in Mailcoach API')">
            <div>
                <x-mailcoach::help>
                    {!! __mc('Whenever you need to specify a <code>:resourceName</code> in the Mailcoach API and want to use this :resource, you\'ll need to pass this value', [
                    'resourceName' => 'segment uuid',
                    'resource' => 'segment',
                ]) !!}
                    <p class="mt-4">
                        <x-mailcoach::code-copy class="flex items-center justify-between max-w-md" :code="$segment->uuid"></x-mailcoach::code-copy>
                    </p>
                </x-mailcoach::help>
            </div>
        </x-mailcoach::fieldset>
        </form>
    @endif

    @if($tab === 'population')
        <livewire:mailcoach::segment-subscribers :emailList="$emailList" :segment="$segment" />
    @endif
</div>
