<?php

namespace Spatie\Mailcoach\Domain\Campaign\Support\Replacers;

use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;

interface CampaignReplacer extends ReplacerWithHelpText
{
    public function replace(string $text, Campaign $campaign): string;
}
