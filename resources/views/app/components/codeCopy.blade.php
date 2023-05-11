@props([
    'code' => '',
    'buttonPosition' => 'bottom',
    'buttonClass' => '',
    'codeClass' => '',
    'lang' => null,
])
<div {{ $attributes->except(['code', 'lang', 'buttonClass'])->merge([
    'class' => $lang ? 'relative markup markup-code -mt-4' : 'p-2 bg-indigo-50'
]) }}>
    @if ($buttonPosition === 'top')
        <div x-data class="{{ $buttonClass }} {{ $lang ? 'absolute text-white pr-2' : 'relative' }} z-20">
            <button type="button" class="text-sm" @click.prevent="$clipboard(@js($code)); $el.innerText = 'Copied!'">
                <i class="fas fa-copy"></i>
            </button>
        </div>
    @endif

    @if ($lang)
        {!! app(\Spatie\Mailcoach\Domain\Shared\Actions\RenderMarkdownToHtmlAction::class)->execute(<<<markdown
        ```{$lang}
        {$code}
        ```
        markdown) !!}
    @else
        <pre class="max-w-full code overflow-x-auto relative z-10 {{ $codeClass }}">{{ $code }}</pre>
    @endif

    @if ($buttonPosition === 'bottom')
    <div x-data>
        <button type="button" class="text-sm link-dimmed" @click.prevent="$clipboard(@js($code)); $el.innerText = 'Copied!'">{{ __mc('Copy') }}</button>
    </div>
    @endif
</div>
