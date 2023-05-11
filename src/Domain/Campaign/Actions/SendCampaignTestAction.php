<?php

namespace Spatie\Mailcoach\Domain\Campaign\Actions;

use Illuminate\Support\Str;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Mailcoach;

class SendCampaignTestAction
{
    use UsesMailcoachModels;

    public function __construct(
        private SendMailAction $sendMailAction
    ) {
    }

    public function execute(Campaign $campaign, string $email): void
    {
        $originalUpdatedAt = $campaign->updated_at;
        $originalSubject = $campaign->subject;
        $campaign->subject = "[Test] {$originalSubject}";

        /** @var \Spatie\Mailcoach\Domain\Campaign\Actions\PrepareSubjectAction $prepareSubjectAction */
        $prepareSubjectAction = Mailcoach::getCampaignActionClass('prepare_subject', PrepareSubjectAction::class);
        $prepareSubjectAction->execute($campaign);

        /** @var \Spatie\Mailcoach\Domain\Campaign\Actions\PrepareEmailHtmlAction $prepareEmailHtmlAction */
        $prepareEmailHtmlAction = Mailcoach::getCampaignActionClass('prepare_email_html', PrepareEmailHtmlAction::class);
        $prepareEmailHtmlAction->execute($campaign);

        if (! $subscriber = self::getSubscriberClass()::where('email', $email)->where('email_list_id', $campaign->email_list_id)->first()) {
            $subscriber = self::getSubscriberClass()::make([
                'uuid' => Str::uuid()->toString(),
                'email_list_id' => $campaign->email_list_id,
                'email' => $email,
            ]);
        }

        $send = self::getSendClass()::make([
            'uuid' => Str::uuid()->toString(),
            'subscriber_id' => $subscriber->id,
            'campaign_id' => $campaign->id,
        ]);
        $send->setRelation('subscriber', $subscriber);
        $send->setRelation('campaign', $campaign);

        try {
            $this->sendMailAction->execute($send, isTest: true);
        } finally {
            $campaign->update([
                'subject' => $originalSubject,
                'updated_at' => $originalUpdatedAt,
            ]);
            $send->delete();
        }
    }
}
