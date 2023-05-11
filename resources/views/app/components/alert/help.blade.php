<div {{ $attributes->merge(['class' => 'ml-2 alert alert-info ' . (isset($full) ? '' : 'md:max-w-xl')]) }}>
    <div class="absolute -left-[8px] -top-[3px] border-4 flex border-white rounded-full">
        <x-mailcoach::rounded-icon type="info" icon="{{ isset($sync) ? 'fas fa-sync fa-spin' : 'fas fa-info' }}" />
    </div>
    <div class="markup markup-links-dimmed">
        {{ $slot }}
    </div>
</div>
