<form
    method="POST"
    class="card-grid"
    wire:submit.prevent="save"
    @keydown.prevent.window.cmd.s="$wire.call('save')"
    @keydown.prevent.window.ctrl.s="$wire.call('save')"
>
    <x-mailcoach::help>
        <p>
            {{ __mc('Mailcoach can create a website that displays the content of each mail you send to this list. This way, people not subscribed to your list can still read your content.') }}
        </p>
    </x-mailcoach::help>
    <x-mailcoach::fieldset :legend="__mc('Settings')" card>
        <x-mailcoach::checkbox-field
            :label="__mc('Enable website')"
            name="emailList.has_website"
            wire:model.lazy="emailList.has_website"
        />
        <x-mailcoach::checkbox-field
            :label="__mc('Show a subscription form')"
            name="emailList.show_subscription_form_on_website"
            wire:model.lazy="emailList.show_subscription_form_on_website"
        />
        <div class="form-field">
            <label class="label" for="emailList.website_slug">{{__mc('Website URL')}}</label>
            <div class="flex items-center">
                <span class="select-none px-3 h-10 flex items-center text-indigo-900/70 -mr-px rounded-l-sm bg-indigo-500/10 border border-r-none border-indigo-700/20 whitespace-nowrap">{{ route('mailcoach.website', '') }}/</span>
                <input id="emailList.website_slug" class="input rounded-r-none" placeholder="/" type="text" name="emailList.website_slug" wire:model.defer="emailList.website_slug" />
                <a class="link ml-2" href="{{ $emailList->websiteUrl() }}" target="_blank">
                    <i class="fas fa-external-link"></i>
                </a>
            </div>
            @error('website_slug')
                <p class="form-error" role="alert">{{ $message }}</p>
            @enderror
        </div>
    </x-mailcoach::fieldset>
    <x-mailcoach::fieldset card :legend="__mc('Customization')">
        <x-mailcoach::color-field
            :label="__mc('Primary Color')"
            name="emailList.website_primary_color"
            wire:model="emailList.website_primary_color"
        />
        <div class="form-field">
            @error('emailList.website_theme')
                <p class="form-error">{{ $message }}</p>
            @enderror
            <label class="label label-required" for="emailList.website_theme">
                {{ __mc('Style') }}
            </label>
            <div class="radio-group">
                <x-mailcoach::radio-field
                    name="emailList.website_theme"
                    option-value="default"
                    wire:model="emailList.website_theme"
                    :label="__mc('Default')"
                />
                <x-mailcoach::radio-field
                    name="emailList.website_theme"
                    option-value="serif"
                    wire:model="emailList.website_theme"
                    :label="__mc('Serif')"
                />
                <x-mailcoach::radio-field
                    name="emailList.website_theme"
                    option-value="typewriter"
                    wire:model="emailList.website_theme"
                    :label="__mc('Typewriter')"
                />
            </div>
        </div>
        <div class="gap-6">
            <div>
                <label class="label" for="image">
                    Header Image
                </label>
                <x-mailcoach::help full class="my-4">
                    {{ __mc('This image will be displayed at the top of your website. Only .jpg and .png are allowed, the maximum size is 2MB.') }}
                </x-mailcoach::help>
            </div>
            <div class="mt-2 max-w-sm">
                <input accept=".jpg,.png" type="file" wire:model="image"/>
                @error('image')
                <p class="form-error mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex items-center gap-4">
                <div wire:loading.delay wire:target="image">
                    <style>
                        @keyframes loadingpulse {
                            0% {
                                transform: scale(.8);
                                opacity: .75
                            }
                            100% {
                                transform: scale(1);
                                opacity: .9
                            }
                        }
                    </style>
                    <span
                        style="animation: loadingpulse 0.75s alternate infinite ease-in-out;"
                        class="group w-8 h-8 inline-flex items-center justify-center bg-gradient-to-b from-blue-500 to-blue-600 text-white rounded-full">
                            <span
                                class="flex items-center justify-center w-6 h-6 transform group-hover:scale-90 transition-transform duration-150">
                                @include('mailcoach::app.layouts.partials.logoSvg')
                            </span>
                        </span>
                    <span class="ml-1 text-gray-700">{{ __mc('Uploading...') }}</span>
                </div>
            </div>
        </div>
        <div>
            @if ($image)
                <img class="max-w-xs" alt="uploaded header image" src="{{ $image->temporaryUrl() }}"/>
            @elseif ($headerImageUrl = $emailList->websiteHeaderImageUrl())
                <img class="max-w-xs" alt="uploaded header image" src="{{ $headerImageUrl }}"/>
            @endif
        </div>
        <x-mailcoach::text-field
            :label="__mc('Website Title')"
            wire:model.lazy="emailList.website_title"
            name="emailList.website_title"
        />
        <x-mailcoach::markdown-field
            :label="__mc('Intro')"
            name="emailList.website_intro"
            wire:model.lazy="emailList.website_intro"
        />
        <x-mailcoach::info class="-mt-12">
            {{ __mc('This text will be displayed at the top of the page.') }}
        </x-mailcoach::info>
    </x-mailcoach::fieldset>
    <x-mailcoach::form-buttons>
        <x-mailcoach::button :label="__mc('Save')"/>
    </x-mailcoach::form-buttons>
</form>

