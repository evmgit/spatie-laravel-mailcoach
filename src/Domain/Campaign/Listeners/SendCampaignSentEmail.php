<?php

namespace Spatie\Mailcoach\Domain\Campaign\Listeners;

use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Domain\Campaign\Events\CampaignSentEvent;
use Spatie\Mailcoach\Domain\Campaign\Mails\CampaignSentMail;

class SendCampaignSentEmail
{
    public function handle(CampaignSentEvent $event)
    {
        $campaign = $event->campaign;

        if (! $campaign->emailList->report_campaign_sent) {
            return;
        }

        Mail::mailer(config('mailcoach.mailer') ?? config('mail.default'))
            ->to($campaign->emailList->campaignReportRecipients())
            ->queue(new CampaignSentMail($campaign));
    }
}
