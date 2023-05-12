<?php

namespace Spatie\Mailcoach\Domain\Shared\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\WebhookClient\Models\WebhookCall;

class CleanupProcessedFeedbackCommand extends Command
{
    use UsesMailcoachModels;

    public $signature = 'mailcoach:cleanup-processed-feedback {--hours=1 : Processed feedback older than this value will be deleted}';

    public $description = 'Cleanup processed feedback';

    protected Carbon $now;

    public function handle()
    {
        $this->comment('Start cleanup...');

        $hours = (int) $this->option('hours');

        WebhookCall::query()
            ->where('processed_at', '<', now()->subHours($hours))
            ->whereIn('name', ['ses-feedback', 'sendgrid-feedback', 'mailgun-feedback', 'postmark-feedback'])
            ->delete();

        $this->comment('All done!');
    }
}
