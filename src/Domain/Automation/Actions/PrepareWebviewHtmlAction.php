<?php

namespace Spatie\Mailcoach\Domain\Automation\Actions;

use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;

class PrepareWebviewHtmlAction
{
    public function execute(AutomationMail $automationMail): void
    {
        $automationMail->webview_html = $automationMail->htmlWithInlinedCss();

        $automationMail->save();
    }
}
