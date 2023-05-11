<?php
    $pendingImportsCount = \Spatie\Mailcoach\Mailcoach::getSubscriberImportClass()::query()
        ->where('email_list_id', $emailList->id)
        ->whereIn('status', [
            \Spatie\Mailcoach\Domain\Audience\Enums\SubscriberImportStatus::Pending,
            \Spatie\Mailcoach\Domain\Audience\Enums\SubscriberImportStatus::Importing,
        ])
        ->count()
?>

<div @if(! $showForm) wire:poll.5s @endif>
    <x-mailcoach::data-table
        name="subscriberImports"
        :rows="$subscriberImports ?? null"
        :total-rows-count="$allSubscriberImportsCount ?? null"
        row-partial="mailcoach::app.emailLists.subscribers.partials.import-row"
        :empty-text="__mc('No imports yet')"
        :searchable="false"
        :columns="[
            ['class' => 'w-32', 'attribute' => 'status', 'label' => __mc('Status')],
            ['class' => 'w-48 th-numeric', 'attribute' => 'created_at', 'label' => __mc('Started at')],
            ['class' => 'th-numeric', 'attribute' => 'imported_subscribers_count', 'label' => __mc('Processed rows')],
            ['class' => 'th-numeric', 'label' => __mc('Errors')],
            ['class' => 'w-12'],
        ]"
    />

    <x-mailcoach::card class="mt-4">
        @if ($showForm)
            <form class="form-grid" method="POST" action="{{ route('mailcoach.emailLists.import-subscribers', $emailList) }}" x-cloak>
                @csrf

                <div class="form-field">
                    @error('replace_tags')
                    <p class="form-error">{{ $message }}</p>
                    @enderror

                    <div class="flex">
                        <label class="label" for="tags_mode">
                            {{ __mc('What should happen with tags on existing subscribers?') }}
                        </label>
                    </div>
                    <div class="radio-group">
                        <x-mailcoach::radio-field
                            name="replace_tags"
                            wire:model="replaceTags"
                            option-value="append"
                            :label="__mc('Append any new tags in the import')"
                        />
                        <x-mailcoach::radio-field
                            name="replace_tags"
                            wire:model="replaceTags"
                            option-value="replace"
                            :label="__mc('Replace all tags by the tags in the import')"
                        />
                    </div>
                </div>

                <div class="form-field">
                    @error('subscribeUnsubscribed')
                    <p class="form-error">{{ $message }}</p>
                    @enderror

                    <div class="radio-group">
                        <x-mailcoach::checkbox-field
                            name="subscribeUnsubscribed"
                            wire:model="subscribeUnsubscribed"
                            :label="__mc('Re-subscribe unsubscribed emails')"
                        />
                    </div>
                </div>

                @if ($subscribeUnsubscribed)
                    <x-mailcoach::warning>
                        {{ __mc('Make sure you have proper consent of the subscribers you\'re resubscribing.') }}
                    </x-mailcoach::warning>
                @endif

                <div class="form-field">
                    @error('unsubscribeMissing')
                    <p class="form-error">{{ $message }}</p>
                    @enderror

                    <div class="radio-group">
                        <x-mailcoach::checkbox-field
                            name="unsubscribeMissing"
                            wire:model="unsubscribeMissing"
                            :label="__mc('Unsubscribe missing emails')"
                        />
                    </div>
                </div>

                @if ($unsubscribeMissing)
                    <x-mailcoach::warning>
                        {{ __mc('This is a dangerous operation, make sure you upload the correct import list') }}
                    </x-mailcoach::warning>
                @endif

                <div class="flex gap-6">
                    <div>
                        <input accept=".csv,.txt,.xlsx" type="file" wire:model="file" />
                        @error('file')
                        <p class="form-error mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex items-center h-10">
                        <i class="far fa-arrow-right text-blue-300"></i>
                    </div>
                    <div class="flex items-center gap-4">
                        <x-mailcoach::button wire:click.prevent="upload" :label="__mc('Import subscribers')" :disabled="!$file" />
                        <div wire:loading.delay wire:target="file">
                            <style>
                                @keyframes loadingpulse {
                                    0%   {transform: scale(.8); opacity: .75}
                                    100% {transform: scale(1); opacity: .9}
                                }
                            </style>
                            <span
                                style="animation: loadingpulse 0.75s alternate infinite ease-in-out;"
                                class="group w-8 h-8 inline-flex items-center justify-center bg-gradient-to-b from-blue-500 to-blue-600 text-white rounded-full">
                            <span class="flex items-center justify-center w-6 h-6 transform group-hover:scale-90 transition-transform duration-150">
                                @include('mailcoach::app.layouts.partials.logoSvg')
                            </span>
                        </span>
                            <span class="ml-1 text-gray-700">Uploading...</span>
                        </div>
                    </div>
                </div>

                <x-mailcoach::info>
                    {!! __mc('Upload a CSV or XLSX file with these columns: email, first_name, last_name, tags <a href=":link" target="_blank">(see documentation)</a>', ['link' => 'https://mailcoach.app/docs/v5/mailcoach/using-mailcoach/audience#content-importing-subscribers']) !!}
                    &mdash; <a href="#" wire:click.prevent="downloadExample" class="link">{{ __mc('Download example') }}</a>
                </x-mailcoach::info>
                <x-mailcoach::info>
                    {!! __mc('Mailcoach also supports the <a href=":url" target="_blank">Mailchimp CSV export</a> format for importing subscribers.', ['url' => 'https://mailchimp.com/help/view-export-contacts/#View_or_export_an_audience']) !!}
                </x-mailcoach::info>
            </form>
        @else
            <div>
                <x-mailcoach::button wire:click.prevent="$set('showForm', true)" :label="__mc('New import')" />
            </div>
        @endif
    </x-mailcoach::card>
</div>
