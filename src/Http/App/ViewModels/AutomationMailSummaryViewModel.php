<?php

namespace Spatie\Mailcoach\Http\App\ViewModels;

use Illuminate\Support\Collection;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\ViewModels\ViewModel;

class AutomationMailSummaryViewModel extends ViewModel
{
    use UsesMailcoachModels;

    protected AutomationMail $mail;

    protected Collection $stats;

    protected int $limit;

    public int $failedSendsCount = 0;

    public function __construct(AutomationMail $mail)
    {
        $this->mail = $mail;

        $this->stats = $this->createStats();

        $this->limit = (ceil(max($this->stats->max('opens'), $this->stats->max('clicks')) * 1.1 / 10) * 10) ?: 1;

        $this->failedSendsCount = $this->mail()->sends()->failed()->count();
    }

    public function mail(): AutomationMail
    {
        return $this->mail;
    }

    public function stats(): Collection
    {
        return $this->stats;
    }

    public function limit(): int
    {
        return $this->limit;
    }

    protected function createStats(): Collection
    {
        $start = $this->mail->created_at->toImmutable();

        if ($this->mail->opens()->count() > 0 && $this->mail->opens()->first()->created_at < $start) {
            $start = $this->mail->opens()->first()->created_at->toImmutable();
        }

        $automationMailOpenTable = static::getAutomationMailOpenTableName();
        $automationMailClickTable = static::getAutomationMailClickTableName();

        return Collection::times(24)->map(function (int $number) use ($start, $automationMailOpenTable, $automationMailClickTable) {
            $datetime = $start->addHours($number - 1);

            return [
                'label' => $datetime->format('H:i'),
                'opens' => $this->mail->opens()->whereBetween("{$automationMailOpenTable}.created_at", [$datetime, $datetime->addHour()])->count(),
                'clicks' => $this->mail->clicks()->whereBetween("{$automationMailClickTable}.created_at", [$datetime, $datetime->addHour()])->count(),
            ];
        });
    }
}
