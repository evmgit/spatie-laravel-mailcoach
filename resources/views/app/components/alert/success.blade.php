<div {{ $attributes->merge(['class' => 'ml-2 alert alert-success ' . (isset($full) ? '' : 'md:max-w-xl')]) }}>
    <div class="absolute -left-[8px] -top-[3px] border-4 flex border-white rounded-full">
        <x-mailcoach::rounded-icon type="success" icon="fas fa-check" />   
    </div> 
    <div class="markup markup-links-dimmed">
        {{ $slot }}
    </div>
</div>
