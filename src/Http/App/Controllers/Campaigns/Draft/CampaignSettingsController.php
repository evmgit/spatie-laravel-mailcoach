<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Campaigns\Draft;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\App\Requests\Campaigns\UpdateCampaignSettingsRequest;

class CampaignSettingsController
{
    use AuthorizesRequests;
    use UsesMailcoachModels;

    public function edit(Campaign $campaign)
    {
        $this->authorize('update', $campaign);

        $emailLists = $this->getEmailListClass()::with('segments')->get();

        $viewName = $campaign->isEditable()
            ? 'settings'
            : 'settingsReadOnly';

        return view("mailcoach::app.campaigns.{$viewName}", [
            'campaign' => $campaign,
            'emailLists' => $emailLists,
            'segmentsData' => $emailLists->map(function (EmailList $emailList) {
                return [
                    'id' => $emailList->id,
                    'name' => $emailList->name,
                    'segments' => $emailList->segments->map->only('id', 'name'),
                    'createSegmentUrl' => route('mailcoach.emailLists.segments', $emailList),
                ];
            }),
        ]);
    }

    public function update(Campaign $campaign, UpdateCampaignSettingsRequest $request)
    {
        $this->authorize('update', $campaign);

        $campaign->fill([
            'name' => $request->name,
            'subject' => $request->subject,
            'track_opens' => $request->track_opens ?? false,
            'track_clicks' => $request->track_clicks ?? false,
            'utm_tags' => $request->utm_tags ?? false,
            'last_modified_at' => now(),
            'email_list_id' => $request->email_list_id,
            'segment_class' => $request->getSegmentClass(),
            'segment_id' => $request->segment_id,
        ]);

        $campaign->save();

        $campaign->update(['segment_description' => $campaign->getSegment()->description()]);

        flash()->success(__('Campaign :campaign was updated.', ['campaign' => $campaign->name]));

        return redirect()->route('mailcoach.campaigns.settings', $campaign->id);
    }
}
