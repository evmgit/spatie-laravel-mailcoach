<div>
    <x-mailcoach::text-field
        :label="__mc('Tag')"
        name="tag"
        :value="$automation->getTrigger()->tag ?? null"
        required
    />
</div>
