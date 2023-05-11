<?php

namespace Spatie\Mailcoach\Domain\Campaign\Jobs;

use Carbon\CarbonInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Campaign\Actions\SendMailAction;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Mailcoach;
use Spatie\RateLimitedMiddleware\RateLimited;

class SendCampaignMailJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public bool $deleteWhenMissingModels = true;

    public int $maxExceptions = 3;

    public Send $pendingSend;

    /** @var string */
    public $queue;

    public $uniqueFor = 45;

    public function uniqueId(): string
    {
        return $this->pendingSend->id;
    }

    public function retryUntil(): CarbonInterface
    {
        return now()->addHour();
    }

    public function __construct(Send $pendingSend)
    {
        $this->pendingSend = $pendingSend;

        $this->queue = config('mailcoach.campaigns.perform_on_queue.send_mail_job');

        $this->connection = $this->connection ?? Mailcoach::getQueueConnection();
    }

    public function handle()
    {
        $campaign = $this->pendingSend->campaign;

        if (! $campaign || $campaign->isCancelled()) {
            if (! $this->pendingSend->wasAlreadySent()) {
                $this->pendingSend->delete();
            }

            return;
        }

        $subscriber = $this->pendingSend->subscriber;

        if (! $campaign->getSegment()->shouldSend($subscriber)) {
            $this->pendingSend->invalidate();

            return;
        }

        if (! $this->isValidSubscriptionForEmailList($subscriber, $campaign->emailList)) {
            $this->pendingSend->invalidate();

            return;
        }

        /** @var \Spatie\Mailcoach\Domain\Campaign\Actions\SendMailAction $sendMailAction */
        $sendMailAction = Mailcoach::getCampaignActionClass('send_mail', SendMailAction::class);

        $sendMailAction->execute($this->pendingSend);
    }

    public function middleware(): array
    {
        if (! $this->pendingSend->campaign) {
            return [];
        }

        if ($this->pendingSend->campaign->isCancelled()) {
            return [];
        }

        $mailer = $this->pendingSend->campaign->getMailerKey();

        $rateLimitedMiddleware = (new RateLimited(useRedis: false))
            ->key('mailer-throttle-'.$mailer)
            ->allow(config("mail.mailers.{$mailer}.mails_per_timespan", 10))
            ->everySeconds(config("mail.mailers.{$mailer}.timespan_in_seconds", 1))
            ->releaseAfterSeconds(config("mail.mailers.{$mailer}.timespan_in_seconds", 1) + 1);

        return [$rateLimitedMiddleware];
    }

    protected function isValidSubscriptionForEmailList(Subscriber $subscriber, EmailList $emailList): bool
    {
        if (! $subscriber->isSubscribed()) {
            return false;
        }

        if ((int) $subscriber->email_list_id !== (int) $emailList->id) {
            return false;
        }

        return true;
    }
}
