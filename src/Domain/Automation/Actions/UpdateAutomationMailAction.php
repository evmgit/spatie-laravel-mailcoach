<?php

namespace Spatie\Mailcoach\Domain\Automation\Actions;

use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class UpdateAutomationMailAction
{
    use UsesMailcoachModels;

    public function execute(AutomationMail $automationMail, array $attributes): AutomationMail
    {
        $automationMail->fill([
            'name' => $attributes['name'],
            'subject' => $attributes['subject'] ?? $attributes['name'],
            'html' => $attributes['html'] ?? '',
            'structured_html' => $attributes['structured_html'] ?? '',
            'utm_tags' => $attributes['utm_tags'] ?? config('mailcoach.automation.default_settings.utm_tags', false),
            'last_modified_at' => now(),
        ]);

        $automationMail->save();

        return $automationMail->refresh();
    }
}
