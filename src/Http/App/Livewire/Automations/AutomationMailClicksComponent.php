<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Automations;

use Illuminate\Http\Request;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Http\App\Livewire\DataTableComponent;
use Spatie\Mailcoach\Http\App\Queries\AutomationMailLinksQuery;
use Spatie\Mailcoach\MainNavigation;

class AutomationMailClicksComponent extends DataTableComponent
{
    public string $sort = '-unique_click_count';

    public AutomationMail $mail;

    public function mount(AutomationMail $automationMail)
    {
        $this->mail = $automationMail;

        app(MainNavigation::class)->activeSection()?->add($this->mail->name, route('mailcoach.automations.mails'));
    }

    public function getTitle(): string
    {
        return __mc('Clicks');
    }

    public function getView(): string
    {
        return 'mailcoach::app.automations.mails.clicks';
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
        return [
            'mail' => $this->mail,
            'links' => (new AutomationMailLinksQuery($this->mail, $request))->paginate($request->per_page),
            'totalLinksCount' => $this->mail->links()->count(),
        ];
    }
}
