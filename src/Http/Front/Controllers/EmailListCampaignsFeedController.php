<?php

namespace Spatie\Mailcoach\Http\Front\Controllers;

use Spatie\Feed\Feed;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class EmailListCampaignsFeedController
{
    use UsesMailcoachModels;

    public function __invoke(string $emailListUuid)
    {
        if (! $emailList = $this->getEmailListClass()::findByUuid($emailListUuid)) {
            abort(404);
        }

        if (! $emailList->campaigns_feed_enabled) {
            abort(404);
        }

        $campaigns = $emailList->campaigns()
            ->showPublicly()
            ->orderByDesc('sent_at')
            ->take(50)
            ->get();

        return new Feed("{$emailList->name} campaigns", $campaigns);
    }
}
