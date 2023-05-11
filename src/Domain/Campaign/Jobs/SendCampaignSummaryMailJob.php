<?php

namespace Spatie\Mailcoach\Domain\Campaign\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Domain\Campaign\Mails\CampaignSummaryMail;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Mailcoach;

class SendCampaignSummaryMailJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use UsesMailcoachModels;

    public int $uniqueFor = 60;

    public function __construct()
    {
        $this->onQueue(config('mailcoach.shared.perform_on_queue.schedule'));
    }

    public function handle()
    {
        self::getCampaignClass()::query()
            ->needsSummaryToBeReported()
            ->sentDaysAgo(1)
            ->get()
            ->each(function (Campaign $campaign) {
                Mail::mailer(Mailcoach::defaultTransactionalMailer())
                    ->to($campaign->emailList->campaignReportRecipients())
                    ->send(new CampaignSummaryMail($campaign));

                info("Summary mail sent for campaign `{$campaign->name}`");
                $campaign->update(['summary_mail_sent_at' => now()]);
            });
    }
}
