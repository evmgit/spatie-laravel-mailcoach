<?php

namespace Spatie\Mailcoach\Domain\Campaign\Actions;

use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;

class PrepareWebviewHtmlAction
{
    public function execute(Campaign $campaign): void
    {
        $campaign->webview_html = $campaign->htmlWithInlinedCss();

        $campaign->save();
    }
}
