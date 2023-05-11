<?php

namespace Spatie\Mailcoach\Http\App\Livewire\TransactionalMails;

use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMail;
use Spatie\Mailcoach\Http\App\Livewire\LivewireFlash;
use Spatie\Mailcoach\MainNavigation;
use Spatie\ValidationRules\Rules\Delimited;

class TransactionalTemplateContentComponent extends Component
{
    use AuthorizesRequests;
    use LivewireFlash;
    use UsesMailcoachModels;

    public TransactionalMail $template;

    protected $listeners = [
        'editorSaved' => 'save',
    ];

    protected function rules(): array
    {
        return [
            'template.name' => '',
            'template.type' => '',
            'template.subject' => 'required',
            'template.to' => new Delimited('email'),
            'template.cc' => new Delimited('email'),
            'template.bcc' => new Delimited('email'),
            'template.body' => '',
            'template.structured_html' => '',
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

        $attributes = [
            'name' => $this->template->name,
            'type' => $this->template->type,
            'subject' => $this->template->subject,
            'to' => $this->delimitedToArray($this->template->to),
            'cc' => $this->delimitedToArray($this->template->cc),
            'bcc' => $this->delimitedToArray($this->template->bcc),
        ];

        $this->template->fresh()->update($attributes);

        if ($this->template->type !== 'html') {
            $this->template->update([
                'body' => $this->template->body,
                'structured_html' => $this->template->structured_html,
            ]);

            $this->flash(__mc('Template :template was updated.', ['template' => $this->template->name]));
        }
    }

    public function render(): View
    {
        $this->template->to = $this->template->toString();
        $this->template->cc = $this->template->ccString();
        $this->template->bcc = $this->template->bccString();

        return view('mailcoach::app.transactionalMails.templates.edit')
            ->layout('mailcoach::app.transactionalMails.templates.layouts.template', [
                'title' => __mc('Details'),
                'template' => $this->template,
            ]);
    }

    protected function delimitedToArray(?string $value): array
    {
        if (empty($value)) {
            return [];
        }

        return array_map('trim', explode(',', $value));
    }
}
