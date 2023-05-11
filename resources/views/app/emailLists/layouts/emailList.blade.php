<x-mailcoach::layout
    :originTitle="$originTitle ?? $emailList->name"
    :originHref="$originHref ?? null"
    :title="$title ?? null"
    :hideCard="isset($hideCard) ? true : false"
>
    <x-slot name="nav">
        <x-mailcoach::navigation>
            <x-slot:title>
                <div class="mb-6 flex items-center justify-between">
                    <h2 class="font-extrabold text-sm uppercase tracking-wider truncate">{{ $emailList->name }}</h2>
                    @if (Auth::guard(config('mailcoach.guard'))->user()->can('create', \Spatie\Mailcoach\Mailcoach::getCampaignClass()) || Auth::user()->can('create', \Spatie\Mailcoach\Mailcoach::getAutomationClass()))
                        <x-mailcoach::dropdown>
                            <x-slot:trigger>
                                <div class="button text-sm p-0 flex items-center justify-center w-6 h-6">
                                    <i class="far fa-plus transition-all" :class="open ? 'rotate-90' : ''"></i>
                                </div>
                            </x-slot:trigger>

                            @can('create', \Spatie\Mailcoach\Mailcoach::getCampaignClass())
                                <a href="#" x-on:click.prevent="$store.modals.open('create-campaign')" class="text-sm flex items-center text-gray-600 hover:text-blue-700 gap-x-2 underline">
                                    {!! str_replace(' ', '&nbsp;', __mc('Create campaign')) !!}
                                </a>
                                <x-mailcoach::modal :title="__mc('Create campaign')" name="create-campaign">
                                    @livewire('mailcoach::create-campaign', [
                                        'emailList' => $emailList,
                                    ])
                                </x-mailcoach::modal>
                            @endcan
                            @can('create', \Spatie\Mailcoach\Mailcoach::getAutomationClass())
                                <a href="#" x-on:click.prevent="$store.modals.open('create-automation')" class="text-sm flex items-center text-gray-600 hover:text-blue-700 gap-x-2 underline">
                                    {!! str_replace(' ', '&nbsp;', __mc('Create automation')) !!}
                                </a>
                                <x-mailcoach::modal :title="__mc('Create automation')" name="create-automation">
                                    @livewire('mailcoach::create-automation', [
                                        'emailList' => $emailList,
                                    ])
                                </x-mailcoach::modal>
                            @endcan
                        </x-mailcoach::dropdown>
                    @endif
                </div>
            </x-slot:title>
            <x-mailcoach::navigation-item :href="route('mailcoach.emailLists.summary', $emailList)">
                {{__mc('Performance')}}
            </x-mailcoach::navigation-item>
            <x-mailcoach::navigation-item :active="Route::is('mailcoach.emailLists.subscriber*')" :href="route('mailcoach.emailLists.subscribers', $emailList)">
                <span class="flex items-center">
                    {{ __mc('Subscribers')}}
                    <span class="counter mx-2">{{ number_format($emailList->subscribers()->count()) }}</span>
                </span>
            </x-mailcoach::navigation-item>
            <x-mailcoach::navigation-item :active="Route::is('mailcoach.emailLists.tags.*')" :href="route('mailcoach.emailLists.tags', $emailList) . '?type=default'">
                {{ __mc('Tags') }}
            </x-mailcoach::navigation-item>
            <x-mailcoach::navigation-item :active="Route::is('mailcoach.emailLists.segments.*')" :href="route('mailcoach.emailLists.segments', $emailList)">
                {{ __mc('Segments') }}
            </x-mailcoach::navigation-item>

            <x-mailcoach::navigation-group icon="fas fa-cog" :title="__mc('Settings')" :href="route('mailcoach.emailLists.general-settings', $emailList)">
                <x-mailcoach::navigation-item :href="route('mailcoach.emailLists.general-settings', $emailList)">
                    {{ __mc('General') }}
                </x-mailcoach::navigation-item>
                <x-mailcoach::navigation-item :href="route('mailcoach.emailLists.onboarding', $emailList)">
                    {{ __mc('Onboarding') }}
                </x-mailcoach::navigation-item>
                <x-mailcoach::navigation-item :href="route('mailcoach.emailLists.mailers', $emailList)">
                    {{ __mc('Mailers') }}
                </x-mailcoach::navigation-item>
                <x-mailcoach::navigation-item :href="route('mailcoach.emailLists.website', $emailList)">
                    {{ __mc('Website') }}
                </x-mailcoach::navigation-item>
            </x-mailcoach::navigation-group>

            @include('mailcoach::app.emailLists.layouts.partials.afterLastTab')
        </x-mailcoach::navigation>
    </x-slot>

    @if ($emailList->subscribers()->count() === 0 && !Route::is('mailcoach.emailLists.subscriber*'))
        <x-mailcoach::help class="mb-4">
            {!! __mc('This list is empty. <a href=":url">Add some subscribers</a>', ['url' => route('mailcoach.emailLists.subscribers', $emailList)]) !!}
        </x-mailcoach::help>
    @endif

    {{ $slot }}
</x-mailcoach::layout>
