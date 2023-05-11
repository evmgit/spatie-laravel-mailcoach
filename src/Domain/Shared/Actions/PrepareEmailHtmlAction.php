<?php

namespace Spatie\Mailcoach\Domain\Shared\Actions;

use Spatie\Mailcoach\Domain\Automation\Exceptions\CouldNotSendAutomationMail;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Campaign\Actions\AddUtmTagsToHtmlAction;
use Spatie\Mailcoach\Domain\Campaign\Exceptions\CouldNotSendCampaign;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Models\Sendable;
use Throwable;

class PrepareEmailHtmlAction
{
    public function __construct(
        protected ReplacePlaceholdersAction $replacePlaceholdersAction,
        protected AddUtmTagsToHtmlAction $addUtmTagsToHtmlAction,
        protected CreateDomDocumentFromHtmlAction $createDomDocumentFromHtmlAction,
    ) {
    }

    public function execute(Sendable $sendable): void
    {
        $this->ensureValidHtml($sendable);

        $sendable->email_html = $sendable->htmlWithInlinedCss();

        $this->replacePlaceholders($sendable);

        if ($sendable->utm_tags) {
            $sendable->email_html = $this->addUtmTagsToHtmlAction->execute($sendable->email_html, $sendable->name);
        }

        $sendable->email_html = mb_convert_encoding($sendable->email_html, 'UTF-8');

        $sendable->save();
    }

    protected function ensureValidHtml(Sendable $sendable)
    {
        try {
            $this->createDomDocumentFromHtmlAction->execute($sendable->html, false);

            return true;
        } catch (Throwable $exception) {
            if ($sendable instanceof Campaign) {
                throw CouldNotSendCampaign::invalidContent($sendable, $exception);
            }

            if ($sendable instanceof AutomationMail) {
                throw CouldNotSendAutomationMail::invalidContent($sendable, $exception);
            }

            throw $exception;
        }
    }

    protected function replacePlaceholders(Sendable $sendable): void
    {
        $sendable->email_html = $this->replacePlaceholdersAction->execute($sendable->email_html, $sendable);
    }
}
