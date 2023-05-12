<?php

namespace Spatie\Mailcoach\Domain\Campaign\Mails;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;

class CampaignSentMail extends Mailable implements ShouldQueue
{
    public $theme = 'mailcoach::mails.layout.mailcoach';

    public Campaign $campaign;

    public string $summaryUrl;

    public function __construct(Campaign $campaign)
    {
        $this->campaign = $campaign;

        $this->summaryUrl = route('mailcoach.campaigns.summary', $this->campaign);
    }

    public function build()
    {
        $this
            ->from(
                $this->campaign->emailList->default_from_email,
                $this->campaign->emailList->default_from_name
            )
            ->subject(__("The campaign named ':campaignName' has been sent", ['campaignName' => $this->campaign->name]))
            ->markdown('mailcoach::mails.campaignSent');
    }
}
