<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Automations;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Automation\Actions\CreateAutomationAction;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class CreateAutomationComponent extends Component
{
    use UsesMailcoachModels;
    use AuthorizesRequests;

    public array $emailListOptions;

    public ?string $name = null;

    public int|string|null $email_list_id = null;

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

        $this->email_list_id = $emailList?->id ?? array_key_first($this->emailListOptions);
    }

    public function saveAutomation()
    {
        $automation = resolve(CreateAutomationAction::class)->execute(
            $this->validate(),
        );

        flash()->success(__mc('Automation :automation was created.', ['automation' => $automation->name]));

        return redirect()->route('mailcoach.automations.settings', $automation);
    }

    public function render()
    {
        return view('mailcoach::app.automations.partials.create');
    }
}
