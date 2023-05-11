<?php

namespace Spatie\Mailcoach\Http\Livewire;

use Illuminate\Validation\Rule;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Settings\Models\WebhookConfiguration;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\App\Livewire\LivewireFlash;

class EditWebhookComponent extends Component
{
    use LivewireFlash;
    use UsesMailcoachModels;

    public WebhookConfiguration $webhook;

    public array $email_lists;

    public function rules(): array
    {
        return [
            'webhook.name' => ['required'],
            'webhook.url' => ['required', 'url', 'starts_with:https'],
            'webhook.secret' => ['required'],
            'webhook.use_for_all_lists' => ['boolean'],
            'email_lists' => ['nullable', 'array', 'required_if:webhook.use_for_all_lists,false'],
            'email_lists.*' => [Rule::exists(self::getEmailListTableName(), 'id')],
        ];
    }

    public function mount(WebhookConfiguration $webhook)
    {
        $this->webhook = $webhook;

        $this->email_lists = $webhook->emailLists->pluck('id')->values()->toArray();
    }

    public function save()
    {
        $this->webhook->update($this->validate()['webhook']);
        $this->webhook->emailLists()->sync($this->email_lists);

        $this->flash(__mc('The webhook has been updated.'));
    }

    public function render()
    {
        $emailListNames = self::getEmailListClass()::query()
            ->pluck('name', 'id')
            ->toArray();

        return view('mailcoach::app.configuration.webhooks.edit', [
            'emailListNames' => $emailListNames,
        ])->layout('mailcoach::app.layouts.settings', [
            'title' => $this->webhook->name,
        ]);
    }
}
