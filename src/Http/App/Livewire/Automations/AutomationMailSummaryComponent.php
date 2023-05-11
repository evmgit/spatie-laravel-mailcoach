<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Automations;

use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Collection;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\MainNavigation;

class AutomationMailSummaryComponent extends Component
{
    use AuthorizesRequests;
    use UsesMailcoachModels;

    public AutomationMail $mail;

    protected Collection $stats;

    protected int $limit;

    public int $failedSendsCount = 0;

    public function mount(AutomationMail $automationMail)
    {
        $this->mail = $automationMail;

        $this->authorize('view', $automationMail);

        app(MainNavigation::class)->activeSection()?->add($this->mail->name, route('mailcoach.automations.mails'));
    }

    public function render(): View
    {
        $this->stats = $this->createStats();

        $this->limit = (ceil(max($this->stats->max('opens'), $this->stats->max('clicks')) * 1.1 / 10) * 10) ?: 1;

        $this->failedSendsCount = $this->mail->sends()->failed()->count();

        return view('mailcoach::app.automations.mails.summary')
            ->layout('mailcoach::app.automations.mails.layouts.automationMail', [
                'title' => __mc('Performance'),
                'mail' => $this->mail,
            ]);
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
