<?php

namespace Spatie\Mailcoach\Domain\Campaign\Support\Replacers;

use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Campaign\Support\Replacers\Concerns\ReplacesModelAttributes;

class EmailListCampaignReplacer implements CampaignReplacer
{
    use ReplacesModelAttributes;

    public function helpText(): array
    {
        return [
            'list.name' => __('The name of the email list this campaign is sent to'),
        ];
    }

    public function replace(string $text, Campaign $campaign): string
    {
        if (! $campaign->emailList) {
            return $text;
        }

        return $this->replaceModelAttributes($text, 'list', $campaign->emailList);
    }
}
