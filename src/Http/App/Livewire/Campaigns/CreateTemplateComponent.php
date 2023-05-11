<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Campaigns;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Campaign\Actions\Templates\CreateTemplateAction;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class CreateTemplateComponent extends Component
{
    use UsesMailcoachModels;
    use AuthorizesRequests;

    public ?string $name = null;

    protected function rules()
    {
        return [
            'name' => ['required'],
        ];
    }

    public function saveTemplate()
    {
        $this->authorize('create', self::getTemplateClass());

        $template = resolve(CreateTemplateAction::class)->execute(
            $this->validate(),
        );

        flash()->success(__mc('Template :template was created.', ['template' => $template->name]));

        return redirect()->route('mailcoach.templates.edit', $template);
    }

    public function render()
    {
        return view('mailcoach::app.templates.partials.create');
    }
}
