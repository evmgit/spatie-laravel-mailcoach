<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Automations;

use Illuminate\Http\Request;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Http\App\Livewire\DataTableComponent;
use Spatie\Mailcoach\Http\App\Queries\AutomatedMailQuery;

class AutomationMailsComponent extends DataTableComponent
{
    public function duplicateAutomationMail(int $id)
    {
        $automationMail = self::getAutomationMailClass()::find($id);

        $this->authorize('create', $automationMail);

        /** @var AutomationMail $automationMail */
        $automationMail = self::getAutomationMailClass()::create([
            'name' => __mc('Duplicate of').' '.$automationMail->name,
            'subject' => $automationMail->subject,
            'template_id' => $automationMail->template_id,
            'html' => $automationMail->html,
            'structured_html' => $automationMail->structured_html,
            'webview_html' => $automationMail->webview_html,
            'utm_tags' => $automationMail->utm_tags,
            'last_modified_at' => now(),
        ]);

        flash()->success(__mc('Email :name was duplicated.', ['name' => $automationMail->name]));

        return redirect()->route('mailcoach.automations.mails.settings', $automationMail);
    }

    public function deleteAutomationMail(int $id)
    {
        $automationMail = self::getAutomationMailClass()::find($id);

        $this->authorize('delete', $automationMail);

        $automationMail->delete();

        $this->flash(__mc('Automation Email :automationMail was deleted.', ['automationMail' => $automationMail->name]));
    }

    public function getTitle(): string
    {
        return __mc('Emails');
    }

    public function getView(): string
    {
        return 'mailcoach::app.automations.mails.index';
    }

    public function getData(Request $request): array
    {
        return [
            'automationMails' => (new AutomatedMailQuery($request))->paginate($request->per_page),
            'totalAutomationMailsCount' => self::getAutomationMailClass()::count(),
        ];
    }
}
