<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Automations;

use Illuminate\Http\Request;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Http\App\Livewire\DataTableComponent;
use Spatie\Mailcoach\Http\App\Queries\AutomationMailOpensQuery;
use Spatie\Mailcoach\MainNavigation;

class AutomationMailOpensComponent extends DataTableComponent
{
    public string $sort = '-first_opened_at';

    public AutomationMail $mail;

    public function mount(AutomationMail $automationMail)
    {
        $this->mail = $automationMail;

        app(MainNavigation::class)->activeSection()?->add($this->mail->name, route('mailcoach.automations.mails'));
    }

    public function getTitle(): string
    {
        return __mc('Opens');
    }

    public function getView(): string
    {
        return 'mailcoach::app.automations.mails.opens';
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
        $automationMailOpens = (new AutomationMailOpensQuery($this->mail, $request));

        return [
            'mail' => $this->mail,
            'mailOpens' => $automationMailOpens->paginate($request->per_page),
            'totalMailOpensCount' => $automationMailOpens->totalCount,
        ];
    }
}
