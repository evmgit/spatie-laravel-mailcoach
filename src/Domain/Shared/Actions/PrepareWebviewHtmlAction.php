<?php

namespace Spatie\Mailcoach\Domain\Shared\Actions;

use Spatie\Mailcoach\Domain\Campaign\Actions\AddUtmTagsToHtmlAction;
use Spatie\Mailcoach\Domain\Shared\Models\Sendable;

class PrepareWebviewHtmlAction
{
    public function __construct(
        protected ReplacePlaceholdersAction $replacePlaceholdersAction,
        protected AddUtmTagsToHtmlAction $addUtmTagsToHtmlAction,
    ) {
    }

    public function execute(Sendable $sendable): void
    {
        $sendable->webview_html = $sendable->htmlWithInlinedCss();
        $sendable->webview_html = $this->replacePlaceholdersAction->execute($sendable->webview_html, $sendable);

        if ($sendable->utm_tags) {
            $sendable->webview_html = $this->addUtmTagsToHtmlAction->execute($sendable->webview_html, $sendable->name);
        }

        $sendable->webview_html = mb_convert_encoding($sendable->webview_html, 'UTF-8');

        $sendable->save();
    }
}
