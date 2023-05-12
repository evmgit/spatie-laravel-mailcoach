<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Automations\AutomationMails;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class DuplicateAutomationMailController
{
    use AuthorizesRequests;
    use UsesMailcoachModels;

    public function __invoke(AutomationMail $automationMail)
    {
        $this->authorize('create', $automationMail);

        /** @var AutomationMail $automationMail */
        $automationMail = $this->getAutomationMailClass()::create([
            'name' => __('Duplicate of') . ' ' . $automationMail->name,
            'subject' => $automationMail->subject,
            'html' => $automationMail->html,
            'structured_html' => $automationMail->structured_html,
            'webview_html' => $automationMail->webview_html,
            'track_opens' => $automationMail->track_opens,
            'track_clicks' => $automationMail->track_clicks,
            'utm_tags' => $automationMail->utm_tags,
            'last_modified_at' => now(),
        ]);

        flash()->success(__('Email :name was duplicated.', ['name' => $automationMail->name]));

        return redirect()->route('mailcoach.automations.mails.settings', $automationMail);
    }
}
