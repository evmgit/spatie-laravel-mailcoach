<form
        x-data="{
        post: @entangle('emailList.allow_form_subscriptions'),
        confirmation: @entangle('emailList.requires_confirmation'),
        confirmationMail: @entangle('confirmation_mail'),
    }"
        method="POST"
        wire:submit.prevent="save"
        @keydown.prevent.window.cmd.s="$wire.call('save')"
        @keydown.prevent.window.ctrl.s="$wire.call('save')"
>
    <div class="card-grid">
        <x-mailcoach::fieldset card :legend="__mc('Subscriptions')">
            <x-mailcoach::info>
                {!! __mc('Learn more about <a href=":link" target="_blank">subscription settings and forms</a>.', ['link' => 'https://mailcoach.app/docs/v5/mailcoach/using-mailcoach/audience#content-onboarding']) !!}
            </x-mailcoach::info>

            <div class="form-field max-w-full">
                <div class="checkbox-group">
                    <x-mailcoach::checkbox-field
                            :label="__mc('Require confirmation')"
                            name="emailList.requires_confirmation"
                            x-model="confirmation"
                    />

                    <x-mailcoach::checkbox-field
                            :label="__mc('Allow POST from an external form')"
                            name="emailList.allow_form_subscriptions"
                            x-model="post"
                    />

                    <div x-show="post" class="pl-8 w-full max-w-full overflow-hidden">
                        <x-mailcoach::code-copy button-class="w-full text-right -mb-6" button-position="top" lang="html" :code="$emailList->getSubscriptionFormHtml()"/>
                    </div>
                </div>
            </div>

            <div x-show="post" class="pl-8 max-w-xl">
                <x-mailcoach::tags-field
                    :label="__mc('Optionally, allow following subscriber tags')"
                    name="allowed_form_subscription_tags"
                    :value="$allowed_form_subscription_tags"
                    :tags="$emailList->tags->pluck('name')->unique()->toArray()"
                />
            </div>
            <div x-show="post" class="pl-8 max-w-xl">
                <x-mailcoach::text-field
                    :label="__mc('Optionally, allow following subscriber extra Attributes')"
                    :placeholder="__mc('Attribute(s) comma separated: field1,field2')"
                    name="emailList.allowed_form_extra_attributes"
                    wire:model.lazy="emailList.allowed_form_extra_attributes"
                />
            </div>
            <div x-show="post" class="pl-8 max-w-xl">
                <x-mailcoach::text-field
                    :label="__mc('Honeypot field')"
                    placeholder="honeypot"
                    name="emailList.honeypot_field"
                    wire:model.lazy="emailList.honeypot_field"
                />
            </div>
        </x-mailcoach::fieldset>

        <x-mailcoach::fieldset card :legend="__mc('Landing Pages')">
            <x-mailcoach::info>
                {!! __mc('Leave empty to use the defaults. <a target="_blank" href=":link">Example</a>', ['link' => route("mailcoach.landingPages.example")]) !!}
            </x-mailcoach::info>

            <div x-show="confirmation">
                <x-mailcoach::text-field :label="__mc('Confirm subscription')" placeholder="https://"
                                         name="emailList.redirect_after_subscription_pending"
                                         wire:model.lazy="emailList.redirect_after_subscription_pending" type="text"/>
            </div>
            <x-mailcoach::text-field :label="__mc('Someone subscribed')" placeholder="https://"
                                     name="emailList.redirect_after_subscribed"
                                     wire:model.lazy="emailList.redirect_after_subscribed" type="text"/>
            <x-mailcoach::text-field :label="__mc('Email was already subscribed')" placeholder="https://"
                                     name="emailList.redirect_after_already_subscribed"
                                     wire:model.lazy="emailList.redirect_after_already_subscribed"
                                     type="text"/>
            <x-mailcoach::text-field :label="__mc('Someone unsubscribed')" placeholder="https://"
                                     name="emailList.redirect_after_unsubscribed"
                                     wire:model.lazy="emailList.redirect_after_unsubscribed" type="text"/>
        </x-mailcoach::fieldset>

        <div x-show="confirmation">
            <x-mailcoach::fieldset card :legend="__mc('Confirmation mail')">
                @if(empty($emailList->confirmation_mailable_class))
                    <div class="radio-group">
                        <x-mailcoach::radio-field
                                name="confirmation_mail"
                                option-value="send_default_confirmation_mail"
                                :label="__mc('Send default confirmation mail')"
                                x-model="confirmationMail"
                        />
                        <x-mailcoach::radio-field
                                name="confirmation_mail"
                                option-value="send_custom_confirmation_mail"
                                :label="__mc('Send customized confirmation mail')"
                                x-model="confirmationMail"
                        />
                    </div>

                    <div class="form-grid" x-show="confirmationMail === 'send_custom_confirmation_mail'">
                        @if (count($transactionalMailTemplates))
                            <div class="flex items-center gap-x-2 max-w-sm">
                                <div class="w-full">
                                    <x-mailcoach::select-field
                                        wire:model="emailList.confirmation_mail_id"
                                        name="emailList.confirmation_mail_id"
                                        :options="$transactionalMailTemplates"
                                        :placeholder="__mc('Select a transactional mail template')"
                                    />
                                </div>
                                @if ($emailList->confirmationMail)
                                    <a href="{{ route('mailcoach.transactionalMails.templates.edit', $emailList->confirmationMail) }}" class="link">{{ __mc('Edit') }}</a>
                                @endif
                            </div>
                        @else
                            <x-mailcoach::info>
                                {!! __mc('You need to create a transactional mail template first. <a href=":createLink" class="link">Create one here</a>', [
                                    'createLink' => route('mailcoach.transactionalMails.templates'),
                                ]) !!}
                            </x-mailcoach::info>
                        @endif

                        <x-mailcoach::help class="markup-code lg:max-w-3xl">
                            {{ __mc('You can use the following placeholders in the subject and body of the confirmation mail:') }}
                            <dl class="mt-4 markup-dl">
                                <dt><code>::confirmUrl::</code></dt>
                                <dd>{{ __mc('The URL where the subscription can be confirmed') }}</dd>
                                <dt><code>::subscriber.first_name::</code></dt>
                                <dd>{{ __mc('The first name of the subscriber') }}</dd>
                                <dt><code>::list.name::</code></dt>
                                <dd>{{ __mc('The name of this list') }}</dd>
                            </dl>
                        </x-mailcoach::help>
                    </div>
                @else
                    <x-mailcoach::info>
                        {{ __mc('A custom mailable (:mailable) will be used.', ['mailable' => $emailList->confirmation_mailable_class]) }}
                    </x-mailcoach::info>
                @endif
            </x-mailcoach::fieldset>
        </div>

        <x-mailcoach::fieldset card :legend="__mc('Welcome Mail')">
            <x-mailcoach::help>
                {!! __mc('Check out the <a href=":docsUrl" class="link">documentation</a> to learn how to set up a welcome automation.', [
                    'docsUrl' => 'https://mailcoach.app/docs/cloud/using-mailcoach/automations/creating-automation'
                ]) !!}
            </x-mailcoach::help>
        </x-mailcoach::fieldset>

        <x-mailcoach::card buttons>
            <x-mailcoach::button :label="__mc('Save')"/>
        </x-mailcoach::card>
    </div>
</form>
