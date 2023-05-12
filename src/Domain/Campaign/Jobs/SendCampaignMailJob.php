<?php

namespace Spatie\Mailcoach\Domain\Campaign\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\Mailcoach\Domain\Campaign\Actions\SendMailAction;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Domain\Shared\Support\Config;
use Spatie\RateLimitedMiddleware\RateLimited;

class SendCampaignMailJob implements ShouldQueue
{
    use Batchable;
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public bool $deleteWhenMissingModels = true;

    public Send $pendingSend;

    /** @var string */
    public $queue;

    public function __construct(Send $pendingSend)
    {
        $this->pendingSend = $pendingSend;

        $this->queue = config('mailcoach.campaigns.perform_on_queue.send_mail_job');

        $this->connection = $this->connection ?? Config::getQueueConnection();
    }

    public function handle()
    {
        if (optional($this->batch())->canceled()) {
            if (! $this->pendingSend->wasAlreadySent()) {
                $this->pendingSend->delete();
            }

            return;
        }

        /** @var \Spatie\Mailcoach\Domain\Campaign\Actions\SendMailAction $sendMailAction */
        $sendMailAction = Config::getCampaignActionClass('send_mail', SendMailAction::class);

        $sendMailAction->execute($this->pendingSend);
    }

    public function middleware()
    {
        $throttlingConfig = config('mailcoach.campaigns.throttling');
        $rateLimitDriver = config('mailcoach.shared.rate_limit_driver', 'redis');

        if ($rateLimitDriver === 'redis') {
            $rateLimitedMiddleware = (new RateLimited())
                ->connectionName($throttlingConfig['redis_connection_name']);
        } else {
            $rateLimitedMiddleware = (new RateLimited(useRedis: false));
        }

        $rateLimitedMiddleware->enabled($throttlingConfig['enabled'])
            ->allow($throttlingConfig['allowed_number_of_jobs_in_timespan'])
            ->everySeconds($throttlingConfig['timespan_in_seconds'])
            ->releaseAfterSeconds($throttlingConfig['release_in_seconds']);

        return [$rateLimitedMiddleware];
    }

    public function retryUntil()
    {
        return now()->addHours(config('mailcoach.campaigns.throttling.retry_until_hours', 24));
    }
}
