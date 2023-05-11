<?php

namespace Spatie\Mailcoach\Http\Front\Controllers;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class EmailListWebsiteController
{
    use UsesMailcoachModels;

    public function index(string $emailListWebsiteSlug = '/')
    {
        $emailList = $this->getEmailList($emailListWebsiteSlug);

        /**
         * If the email list website is on the root of the domain
         * visiting a campaign will result in this route being
         * called instead of the show route, call it here.
         */
        if (! $emailList) {
            return $this->show('/', $emailListWebsiteSlug);
        }

        $campaigns = self::getCampaignClass()::query()
            ->where('email_list_id', $emailList->id)
            ->with('emailList')
            ->orderByDesc('sent_at')
            ->sent()
            ->showPublicly()
            ->simplePaginate(15);

        return view('mailcoach::emailListWebsite.index', [
            'campaigns' => $campaigns,
            'emailList' => $emailList,
        ]);
    }

    public function show(string $emailListWebsiteSlug, string $campaignUuid)
    {
        $emailList = $this->getEmailList($emailListWebsiteSlug);

        /**
         * If there is no email list website at the root domain
         * we'll redirect to the Mailcoach dashboard to
         * preserve the old functionality.
         */
        if ($emailListWebsiteSlug === '/' && ! $emailList) {
            return redirect()->route('mailcoach.dashboard');
        }

        if (! $emailList) {
            abort(404);
        }

        /** @var $campaign Campaign */
        if (! $campaign = static::getCampaignClass()::findByUuid($campaignUuid)) {
            abort(404);
        }

        abort_unless($emailList->has_website, 404);
        abort_unless($campaign->show_publicly, 404);
        abort_unless($campaign->isSendingOrSent(), 404);

        return view('mailcoach::emailListWebsite.show', [
            'emailList' => $emailList,
            'campaign' => $campaign,
            'webview' => view('mailcoach::campaign.webview', compact('campaign'))->render(),
        ]);
    }

    protected function getEmailList(string $emailListWebsiteSlug): ?EmailList
    {
        return self::getEmailListClass()::query()
            ->where('has_website', true)
            ->where(function (Builder $query) use ($emailListWebsiteSlug) {
                $query
                    ->where('website_slug', Str::start($emailListWebsiteSlug, '/'))
                    ->orWhere('website_slug', Str::after(Str::start($emailListWebsiteSlug, '/'), '/'))
                    ->when($emailListWebsiteSlug === '/', fn (Builder $query) => $query->orWhereNull('website_slug'));
            })
            ->first();
    }
}
