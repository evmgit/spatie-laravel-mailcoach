<?php

namespace Spatie\Mailcoach\Domain\Campaign\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Domain\Campaign\Mails\CampaignSummaryMail;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class SendCampaignSummaryMailCommand extends Command
{
    use UsesMailcoachModels;

    public $signature = 'mailcoach:send-campaign-summary-mail';

    public $description = 'Send a summary mail to campaigns that have been sent out recently';

    public function handle()
    {
        $this->getCampaignClass()::query()
            ->needsSummaryToBeReported()
            ->sentDaysAgo(1)
            ->get()
            ->each(function (Campaign $campaign) {
                Mail::mailer(config('mailcoach.mailer') ?? config('mail.default'))
                    ->to($campaign->emailList->campaignReportRecipients())
                    ->queue(new CampaignSummaryMail($campaign));

                $campaign->update(['summary_mail_sent_at' => now()]);

                $this->info("Summary mail sent for campaign `{$campaign->name}`");
            });

        $this->comment('All done!');
    }
}
