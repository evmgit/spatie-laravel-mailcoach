<?php

namespace Spatie\Mailcoach\Domain\Campaign\Actions;

use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Campaign\Support\Replacers\CampaignReplacer;

class PrepareSubjectAction
{
    public function execute(Campaign $campaign): void
    {
        $this->replacePlaceholdersInSubject($campaign);

        $campaign->save();
    }

    protected function replacePlaceholdersInSubject(Campaign $campaign): void
    {
        $campaign->subject = collect(config('mailcoach.campaigns.replacers'))
            ->map(fn (string $className) => resolve($className))
            ->filter(fn (object $class) => $class instanceof CampaignReplacer)
            ->reduce(fn (string $subject, CampaignReplacer $replacer) => $replacer->replace($subject, $campaign), $campaign->subject);
    }
}
