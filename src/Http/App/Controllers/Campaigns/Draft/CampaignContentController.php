<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Campaigns\Draft;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Http\App\Requests\Campaigns\UpdateCampaignContentRequest;

class CampaignContentController
{
    use AuthorizesRequests;

    public function edit(Campaign $campaign)
    {
        $this->authorize('update', $campaign);

        $viewName = $campaign->isEditable()
            ? 'content'
            : 'contentReadOnly';

        return view("mailcoach::app.campaigns.{$viewName}", compact('campaign'));
    }

    public function update(Campaign $campaign, UpdateCampaignContentRequest $request)
    {
        $this->authorize('update', $campaign);

        $campaign->update([
            'html' => $request->html,
            'structured_html' => $request->structured_html,
            'last_modified_at' => now(),
        ]);

        flash()->success(__('Campaign :campaign was updated.', ['campaign' => $campaign->name]));

        return redirect()->route('mailcoach.campaigns.content', $campaign->id);
    }
}
