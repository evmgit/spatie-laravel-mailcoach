<div>
    <x-mailcoach::html-field :label="__mc('Body (HTML)')" name="html" :value="old('html', $html)"></x-mailcoach::html-field>
</div>

<x-mailcoach::form-buttons>
    <x-mailcoach::button id="save" :label="__mc('Save content of mail')"/>
    <x-mailcoach::button-secondary x-on:click="$store.modals.open('preview')" :label="__mc('Preview')"/>
    <x-mailcoach::button-secondary x-on:click="$store.modals.open('send-test')" :label="__mc('Send Test')"/>
</x-mailcoach::form-buttons>


