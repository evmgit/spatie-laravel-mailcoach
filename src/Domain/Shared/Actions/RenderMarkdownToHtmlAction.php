<?php

namespace Spatie\Mailcoach\Domain\Shared\Actions;

use Illuminate\Support\HtmlString;
use League\CommonMark\Extension\Autolink\AutolinkExtension;
use League\CommonMark\Extension\Strikethrough\StrikethroughExtension;
use League\CommonMark\Extension\Table\TableExtension;
use Spatie\LaravelMarkdown\MarkdownRenderer;

class RenderMarkdownToHtmlAction
{
    public function __construct(private MarkdownRenderer $renderer)
    {
    }

    public function execute(string $markdown, string $theme = null): HtmlString
    {
        /**
         * When Sidecar Shiki is configured and set up, we want to highlight through
         * that function instead of calling Shiki through node directly.
         */
        if (in_array(\Spatie\SidecarShiki\Functions\HighlightFunction::class, config('sidecar.functions', []))) {
            $this->renderer
                ->highlightCode(false)
                ->addExtension(new \Spatie\SidecarShiki\Commonmark\HighlightCodeExtension($theme ?? 'nord'));
        }

        return new HtmlString($this->renderer
            ->disableAnchors()
            ->addExtension(new TableExtension())
            ->addExtension(new StrikethroughExtension())
            ->addExtension(new AutolinkExtension())
            ->highlightTheme($theme ?? 'nord')
            ->toHtml($markdown));
    }
}
