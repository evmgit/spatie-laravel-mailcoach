<?php

namespace Spatie\Mailcoach\Domain\Campaign\Support\Replacers;

use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;

class WebsiteUrlCampaignReplacer implements CampaignReplacer
{
    public function helpText(): array
    {
        return [
            'websiteUrl' => __mc('This URL will display the website listing all of your campaign. You need to enable the website in the email list settings.'),
        ];
    }

    public function replace(string $text, Campaign $campaign): string
    {
        $websiteUrl = $campaign->emailList->has_website
             ? $campaign->emailList->websiteUrl()
            : '';

        $text = str_ireplace('::websiteUrl::', $websiteUrl, $text);
        $text = str_ireplace(urlencode('::websiteUrl::'), $websiteUrl, $text);

        return $text;
    }
}
