<?php

namespace Spatie\Mailcoach\Domain\Campaign\Listeners;

use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Domain\Campaign\Events\CampaignSentEvent;
use Spatie\Mailcoach\Domain\Campaign\Mails\CampaignSentMail;
use Spatie\Mailcoach\Mailcoach;

class SendCampaignSentEmail
{
    public function handle(CampaignSentEvent $event)
    {
        $campaign = $event->campaign;

        if (! $campaign->emailList->report_campaign_sent) {
            return;
        }

        Mail::mailer(Mailcoach::defaultTransactionalMailer())
            ->to($campaign->emailList->campaignReportRecipients())
            ->queue(new CampaignSentMail($campaign));
    }
}
