<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Automations;

use Illuminate\Http\Request;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Http\App\Livewire\DataTableComponent;
use Spatie\Mailcoach\Http\App\Queries\AutomationMailSendsQuery;
use Spatie\Mailcoach\MainNavigation;

class AutomationMailOutboxComponent extends DataTableComponent
{
    public string $sort = '-sent_at';

    protected array $allowedFilters = [
        'type' => ['except' => ''],
    ];

    public AutomationMail $mail;

    public function mount(AutomationMail $automationMail)
    {
        $this->mail = $automationMail;

        app(MainNavigation::class)->activeSection()?->add($this->mail->name, route('mailcoach.automations.mails'));
    }

    public function getTitle(): string
    {
        return __mc('Outbox');
    }

    public function getView(): string
    {
        return 'mailcoach::app.automations.mails.outbox';
    }

    public function getLayout(): string
    {
        return 'mailcoach::app.automations.mails.layouts.automationMail';
    }

    public function getLayoutData(): array
    {
        return [
            'mail' => $this->mail,
        ];
    }

    public function getData(Request $request): array
    {
        $this->authorize('view', $this->mail);

        $sendsQuery = (new AutomationMailSendsQuery($this->mail, $request));

        return [
            'mail' => $this->mail,
            'sends' => $sendsQuery->paginate($request->per_page),
            'totalSends' => $this->mail->sends()->count(),
            'totalPending' => $this->mail->sends()->pending()->count(),
            'totalSent' => $this->mail->sends()->sent()->count(),
            'totalFailed' => $this->mail->sends()->failed()->count(),
            'totalBounces' => $this->mail->sends()->bounced()->count(),
            'totalComplaints' => $this->mail->sends()->complained()->count(),
        ];
    }
}
