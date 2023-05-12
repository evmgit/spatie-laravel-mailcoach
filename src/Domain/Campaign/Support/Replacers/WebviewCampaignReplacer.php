<?php

namespace Spatie\Mailcoach\Domain\Campaign\Support\Replacers;

use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;

class WebviewCampaignReplacer implements CampaignReplacer
{
    public function helpText(): array
    {
        return [
            'webviewUrl' => __('This URL will display the HTML of the campaign'),
        ];
    }

    public function replace(string $text, Campaign $campaign): string
    {
        $webviewUrl = $campaign->webviewUrl();

        return str_ireplace('::webviewUrl::', $webviewUrl, $text);
    }
}
