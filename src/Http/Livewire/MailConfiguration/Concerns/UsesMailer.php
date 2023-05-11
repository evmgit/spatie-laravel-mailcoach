<?php

namespace Spatie\Mailcoach\Http\Livewire\MailConfiguration\Concerns;

use Spatie\Mailcoach\Domain\Settings\Models\Mailer;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

trait UsesMailer
{
    use UsesMailcoachModels;

    private ?Mailer $mailer = null;

    public function mailer(): Mailer
    {
        if ($this->mailer) {
            return $this->mailer;
        }

        $summaryStepName = $this->summaryStepName();

        $mailerId = $this->state()->forStep($summaryStepName)['mailerId'];

        $this->mailer = self::getMailerClass()::find($mailerId);

        return $this->mailer;
    }

    public function summaryStepName(): string
    {
        return collect($this->allStepNames)->last();
    }
}
