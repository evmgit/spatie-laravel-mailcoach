<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Replacers;

use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;

class WebviewAutomationMailReplacer implements AutomationMailReplacer
{
    public function helpText(): array
    {
        return [
            'webviewUrl' => __('This URL will display the HTML of the automation mail'),
        ];
    }

    public function replace(string $text, AutomationMail $automationMail): string
    {
        $webviewUrl = $automationMail->webviewUrl();

        return str_ireplace('::webviewUrl::', $webviewUrl, $text);
    }
}
