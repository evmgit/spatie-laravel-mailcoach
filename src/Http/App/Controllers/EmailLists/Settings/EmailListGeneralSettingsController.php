<?php

namespace Spatie\Mailcoach\Http\App\Controllers\EmailLists\Settings;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Http\App\Requests\EmailLists\Settings\UpdateEmailListGeneralSettingsRequest;

class EmailListGeneralSettingsController
{
    use AuthorizesRequests;

    public function edit(EmailList $emailList)
    {
        $this->authorize('update', $emailList);

        return view('mailcoach::app.emailLists.settings.general', [
            'emailList' => $emailList,
        ]);
    }

    public function update(EmailList $emailList, UpdateEmailListGeneralSettingsRequest $request)
    {
        $this->authorize('update', $emailList);

        $emailList->update([
            'name' => $request->name,
            'default_from_email' => $request->default_from_email,
            'default_from_name' => $request->default_from_name,
            'default_reply_to_email' => $request->default_reply_to_email,
            'default_reply_to_name' => $request->default_reply_to_name,
            'campaigns_feed_enabled' => $request->campaigns_feed_enabled ?? false,
            'report_recipients' => $request->report_recipients,
            'report_campaign_sent' => $request->report_campaign_sent ?? false,
            'report_campaign_summary' => $request->report_campaign_summary ?? false,
            'report_email_list_summary' => $request->report_email_list_summary ?? false,
        ]);

        flash()->success(__('List :emailList was updated', ['emailList' => $emailList->name]));

        return redirect()->route('mailcoach.emailLists.general-settings', $emailList);
    }
}
