

<div class="flex justify-center -my-1.5">
        <x-mailcoach::dropdown direction="right">
        <x-slot name="trigger">
            <div class="group button button-rounded" title="{{__mc('Insert action')}}">
                <span class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-2 h-px bg-white"></span>
                <span class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-px h-2 bg-white"></span>
            </div>
        </x-slot>

        <div class="p-6">
            @include('mailcoach::app.automations.components.actionCategories')
        </div>

    </x-mailcoach::dropdown>
</div>
