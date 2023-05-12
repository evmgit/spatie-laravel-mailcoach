<x-mailcoach::layout-subscriber :subscriber="$subscriber" :totalSendsCount="$totalSendsCount">
    @if($subscriber->extra_attributes->count())

        <table class="table table-fixed">
            <thead>
            <tr>
                <th>{{ __('Key') }}</th>
                <th>{{ __('Value') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($subscriber->extra_attributes->all() as $key => $attribute)
                <tr>
                    <td class="markup-links">
                        {{ $key }}
                    </td>
                    <td class="td-secondary-line">
                        @if(is_array($attribute))
                            <pre>{{ json_encode($attribute, JSON_PRETTY_PRINT) }}</pre>
                        @else
                            {{ $attribute }}
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>



    @else
        <x-mailcoach::help>
            {{ __("This user doesn't have any attributes yet.") }}
        </x-mailcoach::help>
    @endif
</x-mailcoach::layout-subscriber>
