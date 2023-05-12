<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Campaigns;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Campaign\Models\Template;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\App\Queries\CampaignsQuery;

class CampaignsIndexController
{
    use AuthorizesRequests;
    use UsesMailcoachModels;

    public function __invoke(CampaignsQuery $campaignsQuery)
    {
        $this->authorize("viewAny", static::getCampaignClass());

        return view('mailcoach::app.campaigns.index', [
            'campaigns' => $campaignsQuery->paginate(),
            'campaignsQuery' => $campaignsQuery,
            'totalCampaignsCount' => static::getCampaignClass()::count(),
            'totalListsCount' => static::getEmailListClass()::count(),
            'sentCampaignsCount' => static::getCampaignClass()::sendingOrSent()->count(),
            'scheduledCampaignsCount' => static::getCampaignClass()::scheduled()->count(),
            'draftCampaignsCount' => static::getCampaignClass()::draft()->count(),
            'templateOptions' => static::getTemplateClass()::orderBy('name')->get()
                ->mapWithKeys(fn (Template $template) => [$template->id => $template->name])
                ->prepend('-- None --', 0),
            'emailListOptions' => static::getEmailListClass()::orderBy('name')->get()
                ->mapWithKeys(fn (EmailList $list) => [$list->id => $list->name]),
        ]);
    }
}
