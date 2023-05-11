<div>
    <x-mailcoach::date-time-field
        :label="__mc('Date')"
        name="date"
        :value="$automation->getTrigger()->date ?? null"
        required
    />
</div>
