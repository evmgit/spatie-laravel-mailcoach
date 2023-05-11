<div class="card-grid">
<x-mailcoach::fieldset card :legend="__mc('Opens')">
        @if($transactionalMail->opens->count())
    <table class="mt-0 table-styled">
        <thead>
            <tr>
                <th>{{ __mc('Opened at') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactionalMail->opens as $open)
                <tr>
                    <td>{{ $open->created_at }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @else
        <x-mailcoach::info>{{ __mc('This mail hasn\'t been opened yet.') }}</x-mailcoach::info>
    @endif
</x-mailcoach::fieldset>

<x-mailcoach::fieldset card :legend="__mc('Clicks')">
    @if($transactionalMail->clicksPerUrl()->count())
        <table class="mt-0 table-styled">
            <thead>
                <tr>
                    <th>{{ __mc('URL') }}</th>
                    <th class="th-numeric">{{ __mc('Click count') }}</th>
                    <th class="th-numeric">{{ __mc('First clicked at') }}</th>
                </tr>
            </thead>
            <tbody>
            @foreach($transactionalMail->clicksPerUrl() as $clickGroup)
                <tr class="markup-links">
                    <td><a href="{{ $clickGroup['url'] }}" target="_blank">{{ $clickGroup['url'] }}</a></td>
                    <td class="td-numeric">{{ $clickGroup['count'] }}</td>
                    <td class="td-numeric">{{ $clickGroup['first_clicked_at'] }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @else
        <x-mailcoach::info>
            {{ __mc('No links in this mail have been clicked yet.') }}
        </x-mailcoach::info>
    @endif
</x-mailcoach::fieldset>
</div>
