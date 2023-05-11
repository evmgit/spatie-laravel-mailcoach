<?php

namespace Spatie\Mailcoach\Domain\Automation\Actions;

use Illuminate\Support\Str;
use Spatie\Mailcoach\Domain\Automation\Models\ActionSubscriber;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Mailcoach;

class SendAutomationMailToSubscriberAction
{
    public function execute(AutomationMail $automationMail, ActionSubscriber $actionSubscriber): void
    {
        if (! $actionSubscriber->action->automation->repeat_enabled && $automationMail->wasAlreadySentToSubscriber($actionSubscriber->subscriber)) {
            return;
        }

        $this
            ->prepareSubject($automationMail)
            ->prepareEmailHtml($automationMail)
            ->prepareWebviewHtml($automationMail)
            ->createSend($automationMail, $actionSubscriber);
    }

    protected function prepareSubject(AutomationMail $automationMail): static
    {
        /** @var \Spatie\Mailcoach\Domain\Automation\Actions\PrepareSubjectAction $prepareSubjectAction */
        $prepareSubjectAction = Mailcoach::getAutomationActionClass('prepare_subject', PrepareSubjectAction::class);

        $prepareSubjectAction->execute($automationMail);

        return $this;
    }

    protected function prepareEmailHtml(AutomationMail $automationMail): static
    {
        /** @var \Spatie\Mailcoach\Domain\Automation\Actions\PrepareEmailHtmlAction $prepareEmailHtmlAction */
        $prepareEmailHtmlAction = Mailcoach::getAutomationActionClass('prepare_email_html', PrepareEmailHtmlAction::class);

        $prepareEmailHtmlAction->execute($automationMail);

        return $this;
    }

    protected function prepareWebviewHtml(AutomationMail $automationMail): static
    {
        /** @var \Spatie\Mailcoach\Domain\Automation\Actions\PrepareWebviewHtmlAction $prepareWebviewHtmlAction */
        $prepareWebviewHtmlAction = Mailcoach::getAutomationActionClass('prepare_webview_html', PrepareWebviewHtmlAction::class);

        $prepareWebviewHtmlAction->execute($automationMail);

        return $this;
    }

    protected function createSend(AutomationMail $automationMail, ActionSubscriber $actionSubscriber): Send
    {
        if (! $actionSubscriber->action->automation->repeat_enabled) {
            /** @var \Spatie\Mailcoach\Domain\Shared\Models\Send $pendingSend */
            $pendingSend = $automationMail->sends()
                ->where('subscriber_id', $actionSubscriber->subscriber->id)
                ->first();

            if ($pendingSend) {
                return $pendingSend;
            }
        }

        return $automationMail->sends()->create([
            'subscriber_id' => $actionSubscriber->subscriber->id,
            'uuid' => (string) Str::uuid(),
        ]);
    }
}
