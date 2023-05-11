<x-mailcoach::layout-campaign :title="__mc('Content')" :campaign="$campaign">
    <div class="card-grid" x-data="{ show: 'content' }" x-cloak>
        <nav class="tabs mb-0">
            <ul>
                <x-mailcoach::navigation-item @click.prevent="show = 'content'" x-bind:class="show === 'content' ? 'navigation-item-active' : ''">
                    {{ __mc('Content') }}
                </x-mailcoach::navigation-item>
                <x-mailcoach::navigation-item @click.prevent="show = 'html'" x-bind:class="show === 'html' ? 'navigation-item-active' : ''">
                    {{ __mc('HTML') }}
                </x-mailcoach::navigation-item>
            </ul>
        </nav>

        <x-mailcoach::card x-show="show === 'content'">
            <x-mailcoach::web-view src="{{ $campaign->webviewUrl() }}"/>
        </x-mailcoach::card>

        <x-mailcoach::card x-show="show === 'html'">
            <x-mailcoach::html-field :label="__mc('Body (HTML)')" name="html" :value="$campaign->webview_html ?? $campaign->html" :disabled="! $campaign->isEditable()" />
        </x-mailcoach::card>
    </div>
</x-mailcoach::layout-campaign>
