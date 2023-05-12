<?php

namespace Spatie\Mailcoach\Domain\Campaign\Actions;

use Exception;
use Spatie\Mailcoach\Domain\Campaign\Exceptions\CouldNotSendCampaign;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Campaign\Support\Replacers\CampaignReplacer;
use Spatie\Mailcoach\Domain\Shared\Actions\AddUtmTagsToUrlAction;
use Spatie\Mailcoach\Domain\Shared\Actions\CreateDomDocumentFromHtmlAction;

class PrepareEmailHtmlAction
{
    public function __construct(
        private CreateDomDocumentFromHtmlAction $createDomDocumentFromHtmlAction,
        private AddUtmTagsToUrlAction $addUtmTagsToUrlAction,
    ) {
    }

    public function execute(Campaign $campaign): void
    {
        $this->ensureValidHtml($campaign);

        $campaign->email_html = $campaign->htmlWithInlinedCss();

        $this->replacePlaceholders($campaign);

        if ($campaign->utm_tags) {
            $this->addUtmTags($campaign);
        }

        $campaign->save();
    }

    protected function ensureValidHtml(Campaign $campaign)
    {
        try {
            $this->createDomDocumentFromHtmlAction->execute($campaign->html, false);

            return true;
        } catch (Exception $exception) {
            throw CouldNotSendCampaign::invalidContent($campaign, $exception);
        }
    }

    protected function replacePlaceholders(Campaign $campaign): void
    {
        $campaign->email_html = collect(config('mailcoach.campaigns.replacers'))
            ->map(fn (string $className) => resolve($className))
            ->filter(fn (object $class) => $class instanceof CampaignReplacer)
            ->reduce(fn (string $html, CampaignReplacer $replacer) => $replacer->replace($html, $campaign), $campaign->email_html);
    }

    private function addUtmTags(Campaign $campaign): void
    {
        $document = $this->createDomDocumentFromHtmlAction->execute($campaign->email_html);

        foreach ($document->getElementsByTagName('a') as $linkElement) {
            $url = $linkElement->getAttribute('href');
            $newUrl = $this->addUtmTagsToUrlAction->execute($url, $campaign->name);
            $linkElement->setAttribute('href', $newUrl);
        }

        $campaign->email_html = $document->saveHTML();
    }
}
