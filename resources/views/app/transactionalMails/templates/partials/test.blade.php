<form
    action="{{ route('mailcoach.transactionalMails.templates.sendTestEmail', $template) }}"
    method="POST"
    data-dirty-check
>
    @csrf

    <div class="flex items-end">
        <div class="flex-grow max-w-xl">
            <x-mailcoach::text-field
                :label="__mc('Test addresses')"
                :placeholder="__mc('Email(s) comma separated')"
                name="emails"
                :required="true"
                type="text"
                :value="cache()->get('mailcoach-test-transactional-template-email-addresses')"
            />
        </div>

        <x-mailcoach::button class="ml-2" :label="__mc('Send test')"/>
    </div>
</form>
