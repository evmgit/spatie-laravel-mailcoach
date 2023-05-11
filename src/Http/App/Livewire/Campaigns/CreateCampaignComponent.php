<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Campaigns;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Campaign\Actions\UpdateCampaignAction;
use Spatie\Mailcoach\Domain\Campaign\Models\Template;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class CreateCampaignComponent extends Component
{
    use UsesMailcoachModels;
    use AuthorizesRequests;

    public array $emailListOptions;

    public array $templateOptions;

    public ?string $name = null;

    public int|string|null $email_list_id = null;

    public int|string|null $template_id = null;

    protected function rules()
    {
        return [
            'name' => ['required'],
            'email_list_id' => ['required', Rule::exists(self::getEmailListTableName(), 'id')],
        ];
    }

    public function mount(?EmailList $emailList)
    {
        $this->emailListOptions = static::getEmailListClass()::orderBy('name')->get()
            ->mapWithKeys(fn (EmailList $list) => [$list->id => $list->name])
            ->toArray();

        $this->templateOptions = static::getTemplateClass()::orderBy('name')->get()
            ->mapWithKeys(fn (Template $template) => [$template->id => $template->name])
            ->prepend('-- None --', 0)
            ->toArray();

        $this->email_list_id = $emailList?->id ?? array_key_first($this->emailListOptions);
        $this->template_id = array_key_first($this->templateOptions);
    }

    public function saveCampaign()
    {
        $campaignClass = self::getCampaignClass();

        $this->authorize('create', $campaignClass);

        $campaign = new $campaignClass;

        $campaign = resolve(UpdateCampaignAction::class)->execute(
            $campaign,
            $this->validate(),
            self::getTemplateClass()::find($this->template_id),
        );

        flash()->success(__mc('Campaign :campaign was created.', ['campaign' => $campaign->name]));

        return redirect()->route('mailcoach.campaigns.settings', $campaign);
    }

    public function render()
    {
        return view('mailcoach::app.campaigns.partials.create');
    }
}
