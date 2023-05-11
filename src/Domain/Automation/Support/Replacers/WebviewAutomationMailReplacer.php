<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Replacers;

use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;

class WebviewAutomationMailReplacer implements AutomationMailReplacer
{
    public function helpText(): array
    {
        return [
            'webviewUrl' => __mc('This URL will display the HTML of the automation mail'),
        ];
    }

    public function replace(string $text, AutomationMail $automationMail): string
    {
        $webviewUrl = $automationMail->webviewUrl();

        $text = str_ireplace('::webviewUrl::', $webviewUrl, $text);
        $text = str_ireplace(urlencode('::webviewUrl::'), $webviewUrl, $text);

        return $text;
    }
}
