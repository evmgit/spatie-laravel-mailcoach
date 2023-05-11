<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Audience;

use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\App\Livewire\LivewireFlash;
use Spatie\Mailcoach\MainNavigation;
use Spatie\ValidationRules\Rules\Delimited;

class ListSettingsComponent extends Component
{
    use AuthorizesRequests;
    use UsesMailcoachModels;
    use LivewireFlash;

    public EmailList $emailList;

    protected function rules(): array
    {
        return [
            'emailList.name' => 'required',
            'emailList.default_from_email' => 'required|email:rfc',
            'emailList.default_from_name' => 'nullable',
            'emailList.default_reply_to_email' => 'nullable|email:rfc',
            'emailList.default_reply_to_name' => 'nullable',

            'emailList.campaigns_feed_enabled' => 'boolean',

            'emailList.report_campaign_sent' => 'boolean',
            'emailList.report_campaign_summary' => 'boolean',
            'emailList.report_email_list_summary' => 'boolean',

            'emailList.report_recipients' => [
                new Delimited('email'),
                'required_if:emailList.report_email_list_summary,true',
                'required_if:emailList.report_campaign_sent,true',
                'required_if:emailList.report_campaign_summary,true',
            ],
        ];
    }

    public function mount(EmailList $emailList)
    {
        $this->emailList = $emailList;

        app(MainNavigation::class)->activeSection()?->add($this->emailList->name, route('mailcoach.emailLists.general-settings', $this->emailList));
    }

    public function save()
    {
        $this->validate();

        $this->emailList->save();

        $this->flash(__mc('List :emailList was updated', ['emailList' => $this->emailList->name]));
    }

    public function render(): View
    {
        $this->authorize('update', $this->emailList);

        return view('mailcoach::app.emailLists.settings.general')
            ->layout('mailcoach::app.emailLists.layouts.emailList', [
                'title' => __mc('General'),
                'emailList' => $this->emailList,
            ]);
    }
}
