<?php

namespace Spatie\Mailcoach\Http\App\Livewire\TransactionalMails;

use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMail;
use Spatie\Mailcoach\Http\App\Livewire\LivewireFlash;
use Spatie\Mailcoach\MainNavigation;

class TransactionalTemplateSettingsComponent extends Component
{
    use AuthorizesRequests;
    use LivewireFlash;
    use UsesMailcoachModels;

    public TransactionalMail $template;

    protected function rules(): array
    {
        return [
            'template.name' => 'required',
            'template.type' => 'required',
            'template.store_mail' => '',
        ];
    }

    public function mount(TransactionalMail $transactionalMailTemplate)
    {
        $this->authorize('update', $transactionalMailTemplate);

        $this->template = $transactionalMailTemplate;

        app(MainNavigation::class)->activeSection()?->add($this->template->name, route('mailcoach.transactionalMails.templates'));
    }

    public function save()
    {
        $this->validate();

        $this->template->save();

        $this->flash(__mc('Template :template was updated.', ['template' => $this->template->name]));
    }

    public function render(): View
    {
        return view('mailcoach::app.transactionalMails.templates.settings')
            ->layout('mailcoach::app.transactionalMails.templates.layouts.template', [
                'title' => __mc('Settings'),
                'template' => $this->template,
            ]);
    }
}
