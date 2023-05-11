<?php

namespace Spatie\Mailcoach\Http\App\Livewire\TransactionalMails;

use Illuminate\Http\Request;
use Spatie\Mailcoach\Http\App\Livewire\DataTableComponent;
use Spatie\Mailcoach\Http\App\Livewire\LivewireFlash;
use Spatie\Mailcoach\Http\App\Queries\TransactionalMailTemplateQuery;

class TransactionalMailsComponent extends DataTableComponent
{
    use LivewireFlash;

    public function duplicateTemplate(int $id)
    {
        $template = self::getTransactionalMailClass()::find($id);

        $this->authorize('create', self::getTransactionalMailClass());

        /** @var \Spatie\Mailcoach\Domain\Campaign\Models\Template $duplicateTemplate */
        $duplicateTemplate = $template->replicate()->save();

        flash()->success(__mc('Email :name was duplicated.', ['name' => $template->name]));

        return redirect()->route('mailcoach.transactionalMails.templates.edit', $duplicateTemplate);
    }

    public function deleteTemplate(int $id)
    {
        $template = self::getTransactionalMailClass()::find($id);

        $this->authorize('delete', $template);

        $template->delete();

        $this->flash(__mc('Email :name was deleted.', ['name' => $template->name]));
    }

    public function getTitle(): string
    {
        return __mc('Emails');
    }

    public function getView(): string
    {
        return 'mailcoach::app.transactionalMails.templates.index';
    }

    public function getData(Request $request): array
    {
        $this->authorize('viewAny', static::getTransactionalMailClass());

        return [
            'templates' => (new TransactionalMailTemplateQuery($request))->paginate($request->per_page),
            'templatesCount' => self::getTransactionalMailClass()::count(),
        ];
    }
}
