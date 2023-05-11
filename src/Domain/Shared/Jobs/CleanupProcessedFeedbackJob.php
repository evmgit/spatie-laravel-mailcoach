<?php

namespace Spatie\Mailcoach\Domain\Shared\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\WebhookClient\Models\WebhookCall;

class CleanupProcessedFeedbackJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use UsesMailcoachModels;

    public int $uniqueFor = 60;

    public function __construct(protected int $hours)
    {
        $this->onQueue(config('mailcoach.shared.perform_on_queue.schedule'));
    }

    public function handle()
    {
        WebhookCall::query()
            ->where('processed_at', '<', now()->subHours($this->hours))
            ->whereIn('name', ['ses-feedback', 'sendgrid-feedback', 'mailgun-feedback', 'postmark-feedback', 'sendinblue-feedback'])
            ->delete();
    }
}
