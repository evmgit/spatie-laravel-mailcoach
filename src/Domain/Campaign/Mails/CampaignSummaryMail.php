<?php

namespace Spatie\Mailcoach\Domain\Campaign\Mails;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;

class CampaignSummaryMail extends Mailable implements ShouldQueue
{
    public $theme = 'mailcoach::mails.layout.mailcoach';

    public Campaign $campaign;

    public string $summaryUrl;

    public string $settingsUrl;

    public function __construct(Campaign $campaign)
    {
        $this->campaign = $campaign;

        $this->summaryUrl = route('mailcoach.campaigns.summary', $this->campaign);
        $this->settingsUrl = route('mailcoach.emailLists.general-settings', $this->campaign->emailList);
    }

    public function build()
    {
        $this
            ->from(
                $this->campaign->emailList->default_from_email,
                $this->campaign->emailList->default_from_name
            )
            ->subject(__mc("A summary of the ':campaignName' campaign", ['campaignName' => $this->campaign->name]))
            ->markdown('mailcoach::mails.campaignSummary');
    }
}
