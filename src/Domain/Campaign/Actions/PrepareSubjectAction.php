<?php

namespace Spatie\Mailcoach\Domain\Campaign\Actions;

use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Actions\ReplacePlaceholdersAction;

class PrepareSubjectAction
{
    public function __construct(
        protected ReplacePlaceholdersAction $replacePlaceholdersAction
    ) {
    }

    public function execute(Campaign $campaign): void
    {
        $campaign->subject = $this->replacePlaceholdersAction->execute($campaign->subject, $campaign);
        $campaign->save();
    }
}
