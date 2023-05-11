<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Campaigns;

use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Campaign\Models\Template as TemplateModel;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\App\Livewire\LivewireFlash;

class TemplateComponent extends Component
{
    use AuthorizesRequests;
    use LivewireFlash;
    use UsesMailcoachModels;

    public TemplateModel $template;

    protected $listeners = [
        'editorSaved' => 'save',
    ];

    protected function rules(): array
    {
        return [
            'template.name' => 'required',
            'template.html' => 'required',
        ];
    }

    public function mount(TemplateModel $template)
    {
        $this->authorize('update', $template);

        $this->template = $template;
    }

    public function save()
    {
        $data = $this->validate();

        $this->template->refresh();

        $this->template->name = $data['template']['name'];
        $this->template->save();

        $this->flash(__mc('Template :template was updated.', ['template' => $this->template->name]));
    }

    public function render(): View
    {
        return view('mailcoach::app.templates.edit')
            ->layout('mailcoach::app.layouts.app', [
                'title' => $this->template->name,
            ]);
    }
}
