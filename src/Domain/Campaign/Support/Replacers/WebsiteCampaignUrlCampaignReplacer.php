<?php

namespace Spatie\Mailcoach\Domain\Campaign\Support\Replacers;

use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;

class WebsiteCampaignUrlCampaignReplacer implements CampaignReplacer
{
    public function helpText(): array
    {
        return [
            'websiteCampaignUrl' => __mc('This URL will display the content of your campaign on your campaign\'s website. You need to enable the website in the email list settings.'),
        ];
    }

    public function replace(string $text, Campaign $campaign): string
    {
        $websiteUrl = $campaign->emailList->has_website
             ? $campaign->websiteUrl()
            : '';

        $text = str_ireplace('::websiteCampaignUrl::', $websiteUrl, $text);
        $text = str_ireplace(urlencode('::websiteCampaignUrl::'), $websiteUrl, $text);

        return $text;
    }
}
