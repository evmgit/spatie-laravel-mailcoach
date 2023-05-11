<?php

namespace Spatie\Mailcoach\Domain\Audience\Commands;

use Illuminate\Console\Command;
use Spatie\Mailcoach\Domain\Audience\Jobs\DeleteOldUnconfirmedSubscribersJob;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class DeleteOldUnconfirmedSubscribersCommand extends Command
{
    use UsesMailcoachModels;

    public $signature = 'mailcoach:delete-old-unconfirmed-subscribers';

    public $description = 'Delete all old unconfirmed subscribers';

    public function handle()
    {
        dispatch(new DeleteOldUnconfirmedSubscribersJob());
    }
}
