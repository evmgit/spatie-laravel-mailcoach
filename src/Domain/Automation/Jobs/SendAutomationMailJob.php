<?php

namespace Spatie\Mailcoach\Domain\Automation\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\Mailcoach\Domain\Automation\Actions\SendMailAction;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Domain\Shared\Support\Config;
use Spatie\RateLimitedMiddleware\RateLimited;

class SendAutomationMailJob implements ShouldQueue
{
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

        $this->queue = config('mailcoach.automation.perform_on_queue.send_automation_mail_job');

        $this->connection = $this->connection ?? Config::getQueueConnection();
    }

    public function handle()
    {
        /** @var \Spatie\Mailcoach\Domain\Automation\Actions\SendMailAction $sendMailAction */
        $sendMailAction = Config::getAutomationActionClass('send_mail', SendMailAction::class);

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
