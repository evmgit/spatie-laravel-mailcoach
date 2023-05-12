<?php

namespace Spatie\Mailcoach\Domain\TransactionalMail\Mails\Concerns;

use Illuminate\Mail\Mailable;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Domain\TransactionalMail\Exceptions\CouldNotFindTemplate;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailTemplate;

/** @mixin \Illuminate\Mail\Mailable */
trait UsesMailcoachTemplate
{
    use StoresMail;
    use UsesMailcoachModels;

    public function template(string $name): self
    {
        /** @var TransactionalMailTemplate $template */
        $template = $this->getTransactionalMailTemplateClass()::firstWhere('name', $name);

        if (! $template) {
            throw CouldNotFindTemplate::make($name, $this);
        }

        $this->subject($this->executeReplacers($template->subject, $template, $this));

        if ($template->from) {
            $this->from($template->from);
        }

        foreach ($template->to ?? [] as $to) {
            $this->to($to);
        }

        foreach ($template->cc ?? [] as $cc) {
            $this->cc($cc);
        }

        foreach ($template->bcc ?? [] as $bcc) {
            $this->bcc($bcc);
        }

        $content = $template->render($this);

        $this->view('mailcoach::mails.transactionalMails.template', compact('content'));

        if ($template->track_opens) {
            $this->trackOpens();
        }

        if ($template->track_clicks) {
            $this->trackClicks();
        }

        return $this;
    }

    protected function executeReplacers(string $text, TransactionalMailTemplate $template, Mailable $mailable): string
    {
        foreach ($template->replacers() as $replacer) {
            $text = $replacer->replace($text, $mailable, $template);
        }

        return $text;
    }

    protected static function testInstance(): self
    {
        $instance = new self();
        $template = $instance->getTransactionalMailTemplateClass()::first();
        $instance->subject($instance->executeReplacers($template->subject, $template, $instance));

        return $instance;
    }
}
