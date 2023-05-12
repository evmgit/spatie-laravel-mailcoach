<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Automations\AutomationMails;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Http\App\Requests\Campaigns\UpdateCampaignContentRequest;

class AutomationMailContentController
{
    use AuthorizesRequests;

    public function edit(AutomationMail $mail)
    {
        $this->authorize('update', $mail);

        return view("mailcoach::app.automations.mails.content", compact('mail'));
    }

    public function update(AutomationMail $mail, UpdateCampaignContentRequest $request)
    {
        $this->authorize('update', $mail);

        $mail->update([
            'html' => $request->html,
            'structured_html' => $request->structured_html,
            'last_modified_at' => now(),
        ]);

        flash()->success(__('Email :name was updated.', ['name' => $mail->name]));

        return redirect()->route('mailcoach.automations.mails.content', $mail->id);
    }
}
