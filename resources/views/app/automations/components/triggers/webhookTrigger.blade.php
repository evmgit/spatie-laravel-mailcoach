    <x-mailcoach::help>
    <p>
        Send an authenticated <code>POST</code> request to the following endpoint with an array of subscriber ids, make sure you've set up the <a href="https://mailcoach.app/docs/v4/mailcoach/using-the-api/introduction" target="_blank">Mailcoach API</a>.
    </p>
    <p class="markup-code-block"><code class="whitespace-nowrap">{{ action('\\' . \Spatie\Mailcoach\Http\Api\Controllers\Automations\TriggerAutomationController::class, [$this->automation]) }}</code></p>
    <p class="mt-4">Example POST request:</p>
    <pre class="markup-code-block">
<code class="">$ MAILCOACH_TOKEN="your API token"
$ curl -x POST {{ action('\\' . \Spatie\Mailcoach\Http\Api\Controllers\Automations\TriggerAutomationController::class, [$this->automation]) }} \
    -H "Authorization: Bearer $MAILCOACH_TOKEN" \
    -H 'Accept: application/json' \
    -H 'Content-Type: application/json'
    -d '{"subscribers":[1, 2, 3]}'
</code></pre>

    <p class="my-4">The automation will only trigger for subscribed subscribers of the automation's email list & segment.</p>
</x-mailcoach::help>
