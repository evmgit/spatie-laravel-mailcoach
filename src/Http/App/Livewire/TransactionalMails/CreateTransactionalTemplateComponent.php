<?php

namespace Spatie\Mailcoach\Http\App\Livewire\TransactionalMails;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Domain\TransactionalMail\Actions\CreateTemplateAction;

class CreateTransactionalTemplateComponent extends Component
{
    use UsesMailcoachModels;
    use AuthorizesRequests;

    public ?string $name = null;

    public ?string $type = 'html';

    protected function rules()
    {
        return [
            'name' => ['required'],
            'type' => ['required'],
        ];
    }

    public function saveTemplate()
    {
        $this->authorize('create', self::getTransactionalMailClass());

        $template = resolve(CreateTemplateAction::class)->execute(
            $this->validate(),
        );

        flash()->success(__mc('Email :name was created.', ['name' => $template->name]));

        return redirect()->route('mailcoach.transactionalMails.templates.edit', $template);
    }

    public function render()
    {
        return view('mailcoach::app.transactionalMails.templates.partials.create');
    }
}
