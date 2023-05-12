<?php

namespace Spatie\Mailcoach\Domain\Automation\Actions;

use Exception;
use Spatie\Mailcoach\Domain\Automation\Exceptions\CouldNotSendAutomationMail;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Automation\Support\Replacers\AutomationMailReplacer;
use Spatie\Mailcoach\Domain\Shared\Actions\AddUtmTagsToUrlAction;
use Spatie\Mailcoach\Domain\Shared\Actions\CreateDomDocumentFromHtmlAction;

class PrepareEmailHtmlAction
{
    public function __construct(
        private CreateDomDocumentFromHtmlAction $createDomDocumentFromHtmlAction,
        private AddUtmTagsToUrlAction $addUtmTagsToUrlAction,
    ) {
    }

    public function execute(AutomationMail $automationMail): void
    {
        $this->ensureValidHtml($automationMail);

        $automationMail->email_html = $automationMail->htmlWithInlinedCss();

        $this->replacePlaceholders($automationMail);

        if ($automationMail->utm_tags) {
            $this->addUtmTags($automationMail);
        }

        $automationMail->save();
    }

    protected function ensureValidHtml(AutomationMail $automationMail)
    {
        try {
            $this->createDomDocumentFromHtmlAction->execute($automationMail->html, false);

            return true;
        } catch (Exception $exception) {
            throw CouldNotSendAutomationMail::invalidContent($automationMail, $exception);
        }
    }

    protected function replacePlaceholders(AutomationMail $automationMail): void
    {
        $automationMail->email_html = collect(config('mailcoach.automation.replacers'))
            ->map(fn (string $className) => resolve($className))
            ->filter(fn (object $class) => $class instanceof AutomationMailReplacer)
            ->reduce(fn (string $html, AutomationMailReplacer $replacer) => $replacer->replace($html, $automationMail), $automationMail->email_html);
    }

    private function addUtmTags(AutomationMail $automationMail): void
    {
        $document = $this->createDomDocumentFromHtmlAction->execute($automationMail->email_html);

        foreach ($document->getElementsByTagName('a') as $linkElement) {
            $url = $linkElement->getAttribute('href');
            $newUrl = $this->addUtmTagsToUrlAction->execute($url, $automationMail->name);
            $linkElement->setAttribute('href', $newUrl);
        }

        $automationMail->email_html = $document->saveHTML();
    }
}
