<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Automations\AutomationMails;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\App\Requests\Campaigns\UpdateCampaignSettingsRequest;

class AutomationMailSettingsController
{
    use AuthorizesRequests;
    use UsesMailcoachModels;

    public function edit(AutomationMail $automationMail)
    {
        $this->authorize('update', $automationMail);

        return view("mailcoach::app.automations.mails.settings", [
            'mail' => $automationMail,
        ]);
    }

    public function update(AutomationMail $automationMail, UpdateCampaignSettingsRequest $request)
    {
        $this->authorize('update', $automationMail);

        $automationMail->fill([
            'name' => $request->name,
            'subject' => $request->subject,
            'track_opens' => $request->track_opens ?? false,
            'track_clicks' => $request->track_clicks ?? false,
            'utm_tags' => $request->utm_tags ?? false,
            'last_modified_at' => now(),
        ]);

        $automationMail->save();

        flash()->success(__('Email :name was updated.', ['name' => $automationMail->name]));

        return redirect()->route('mailcoach.automations.mails.settings', $automationMail->id);
    }
}
