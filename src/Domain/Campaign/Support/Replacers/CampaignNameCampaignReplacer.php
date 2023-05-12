<?php

namespace Spatie\Mailcoach\Domain\Campaign\Support\Replacers;

use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Campaign\Support\Replacers\Concerns\ReplacesModelAttributes;

class CampaignNameCampaignReplacer implements CampaignReplacer
{
    use ReplacesModelAttributes;

    public function helpText(): array
    {
        return [
            'campaign.name' => __('The name of this campaign'),
        ];
    }

    public function replace(string $text, Campaign $campaign): string
    {
        return $this->replaceModelAttributes($text, 'campaign', $campaign);
    }
}
