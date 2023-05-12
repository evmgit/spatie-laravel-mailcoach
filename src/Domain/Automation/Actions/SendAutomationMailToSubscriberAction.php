<?php

namespace Spatie\Mailcoach\Domain\Automation\Actions;

use Illuminate\Support\Str;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Jobs\SendAutomationMailJob;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Domain\Shared\Support\Config;

class SendAutomationMailToSubscriberAction
{
    public function execute(AutomationMail $automationMail, Subscriber $subscriber): void
    {
        if ($automationMail->wasAlreadySentToSubscriber($subscriber)) {
            return;
        }

        $this
            ->prepareSubject($automationMail)
            ->prepareEmailHtml($automationMail)
            ->prepareWebviewHtml($automationMail)
            ->sendMail($automationMail, $subscriber);
    }

    protected function prepareSubject(AutomationMail $automationMail): self
    {
        /** @var \Spatie\Mailcoach\Domain\Automation\Actions\PrepareSubjectAction $prepareSubjectAction */
        $prepareSubjectAction = Config::getAutomationActionClass('prepare_subject', PrepareSubjectAction::class);

        $prepareSubjectAction->execute($automationMail);

        return $this;
    }

    protected function prepareEmailHtml(AutomationMail $automationMail): self
    {
        /** @var \Spatie\Mailcoach\Domain\Automation\Actions\PrepareEmailHtmlAction $prepareEmailHtmlAction */
        $prepareEmailHtmlAction = Config::getAutomationActionClass('prepare_email_html', PrepareEmailHtmlAction::class);

        $prepareEmailHtmlAction->execute($automationMail);

        return $this;
    }

    protected function prepareWebviewHtml(AutomationMail $automationMail): self
    {
        /** @var \Spatie\Mailcoach\Domain\Automation\Actions\PrepareWebviewHtmlAction $prepareWebviewHtmlAction */
        $prepareWebviewHtmlAction = Config::getAutomationActionClass('prepare_webview_html', PrepareWebviewHtmlAction::class);

        $prepareWebviewHtmlAction->execute($automationMail);

        return $this;
    }

    protected function createSend(AutomationMail $automationMail, Subscriber $subscriber): Send
    {
        /** @var \Spatie\Mailcoach\Domain\Shared\Models\Send $pendingSend */
        $pendingSend = $automationMail->sends()
            ->where('subscriber_id', $subscriber->id)
            ->first();

        if ($pendingSend) {
            return $pendingSend;
        }

        return $automationMail->sends()->create([
            'subscriber_id' => $subscriber->id,
            'uuid' => (string)Str::uuid(),
        ]);
    }

    protected function sendMail(AutomationMail $automationMail, Subscriber $subscriber)
    {
        $pendingSend = $this->createSend($automationMail, $subscriber);

        dispatch(new SendAutomationMailJob($pendingSend));
    }
}
