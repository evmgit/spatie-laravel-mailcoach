<div>
    <input type="hidden" name="actions" value="{{ json_encode($actions) }}">

    @if(count($actions) == 0) 
        <x-mailcoach::card class="md:p-6 card-top-level">
            @include('mailcoach::app.automations.components.actionCategories')
        </x-mailcoach::card>
    @else
        @foreach ($actions as $index => $action)
            @if($loop->first)
                @include('mailcoach::app.automations.components.actionDropdown', ['index' => $index])
            @endif

            <div>
                @if ($action['class']::getComponent())
                    @livewire($action['class']::getComponent(), array_merge([
                        'index' => $index,
                        'uuid' => $action['uuid'],
                        'action' => $action,
                        'automation' => $automation,
                    ], ($action['data'] ?? [])), key($index . $action['uuid']))
                @else
                    @livewire('mailcoach::automation-action', array_merge([
                        'index' => $index,
                        'uuid' => $action['uuid'],
                        'action' => $action,
                        'automation' => $automation,
                        'editable' => false,
                    ], ($action['data'] ?? [])), key($index . $action['uuid']))
                @endif
            </div>

            @unless($loop->last)
                @include('mailcoach::app.automations.components.actionDropdown', ['index' => $index + 1])
            @endunless
        @endforeach

        @include('mailcoach::app.automations.components.actionDropdown', ['index' => $index + 1])
    @endif

</div>
