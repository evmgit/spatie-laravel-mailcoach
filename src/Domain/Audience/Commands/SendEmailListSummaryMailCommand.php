<?php

namespace Spatie\Mailcoach\Domain\Audience\Commands;

use Illuminate\Console\Command;
use Spatie\Mailcoach\Domain\Audience\Jobs\SendEmailListSummaryMailJob;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class SendEmailListSummaryMailCommand extends Command
{
    use UsesMailcoachModels;

    protected $signature = 'mailcoach:send-email-list-summary-mail';

    public $description = 'Send a summary mail on the subscribers of a list';

    public function handle()
    {
        dispatch(new SendEmailListSummaryMailJob());
    }
}
