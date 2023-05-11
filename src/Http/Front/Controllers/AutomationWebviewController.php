<?php

namespace Spatie\Mailcoach\Http\Front\Controllers;

use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class AutomationWebviewController
{
    use UsesMailcoachModels;

    public function __invoke(string $automationUuid)
    {
        if (! $mail = self::getAutomationMailClass()::findByUuid($automationUuid)) {
            abort(404);
        }

        return view('mailcoach::automation.webview', compact('mail'));
    }
}
