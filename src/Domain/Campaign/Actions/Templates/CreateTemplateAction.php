<?php

namespace Spatie\Mailcoach\Domain\Campaign\Actions\Templates;

use Spatie\Mailcoach\Domain\Campaign\Models\Template;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class CreateTemplateAction
{
    use UsesMailcoachModels;

    public function execute(array $attributes): Template
    {
        return self::getTemplateClass()::create([
            'name' => $attributes['name'],
            'html' => $attributes['html'] ?? <<<'html'
            <!-- You can customise this template however you want -->
            <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
            <html xmlns="http://www.w3.org/1999/xhtml">
            <head>
                <meta name="viewport" content="width=device-width, initial-scale=1.0" />
                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
            </head>

            <body>
                <!-- Body Content -->
                <div style="max-width: 570px; margin: 0 auto; padding: 20px 0;">
                    [[[content]]]
                </div>
                <!-- End Body Content -->

                <div style="text-align: center;">
                    <a href="::webviewUrl::">Online version</a>
                    •
                    <a href="::preferencesUrl::">My preferences</a>
                    •
                    <a href="::unsubscribeUrl::">Unsubscribe</a>
                </div>
            </body>
            </html>
            html,
            'structured_html' => $attributes['structured_html'] ?? null,
        ]);
    }
}
