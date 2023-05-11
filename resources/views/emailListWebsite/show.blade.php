<x-mailcoach::layout-website
    :title="$campaign->subject"
    :email-list="$emailList"
>
    <nav class="back">
        <a href="{{ $emailList->websiteUrl() }}">
            <i>←</i> Back to overview
        </a>
    </nav>
    <div class="show">
        <article class="card">
            <header class="card-header">
                <time datetime="{{ $campaign->sent_at }}">{{ $campaign->sent_at->format('F j, Y') }}</time>
                <h2>{{ $campaign->subject }}</h2>
            </header>
            <div class="webview">
                <x-mailcoach::web-view :html="$webview"/>
            </div>
        </article>
    </div>
    <nav class="back">
        <a href="{{ $emailList->websiteUrl() }}">
            <i>←</i> Back to overview
        </a>
    </nav>
</x-mailcoach::layout-website>
