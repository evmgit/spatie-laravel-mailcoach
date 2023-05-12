<?php

namespace Spatie\Mailcoach\Http\App\Controllers\TransactionalMails\Templates;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Domain\TransactionalMail\Actions\CreateTemplateAction;
use Spatie\Mailcoach\Domain\TransactionalMail\Actions\UpdateTemplateAction;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailTemplate;
use Spatie\Mailcoach\Http\App\Queries\TransactionalMailTemplateQuery;
use Spatie\Mailcoach\Http\App\Requests\TransactionalMails\TransactionalMailTemplateRequest;

class TransactionalMailTemplatesController
{
    use AuthorizesRequests;
    use UsesMailcoachModels;

    public function index(TransactionalMailTemplateQuery $transactionalMailTemplateQuery)
    {
        $this->authorize('viewAny', $this->getTransactionalMailTemplateClass());

        return view('mailcoach::app.transactionalMails.templates.index', [
            'templates' => $transactionalMailTemplateQuery->paginate(),
            'templatesQuery' => $transactionalMailTemplateQuery,
            'templatesCount' => $this->getTransactionalMailTemplateClass()::count(),
        ]);
    }

    public function store(TransactionalMailTemplateRequest $request, CreateTemplateAction $createTemplateAction)
    {
        $this->authorize('create', $this->getTransactionalMailTemplateClass());

        $template = $createTemplateAction->execute($request->validated());

        flash()->success(__('Template :template was created.', ['template' => $template->name]));

        return redirect()->route('mailcoach.transactionalMails.templates.edit', $template);
    }

    public function edit(TransactionalMailTemplate $template)
    {
        $this->authorize('update', $template);

        return view('mailcoach::app.transactionalMails.templates.edit', [
            'template' => $template,
        ]);
    }

    public function update(
        TransactionalMailTemplate $template,
        TransactionalMailTemplateRequest $request,
        UpdateTemplateAction $updateTemplateAction
    ) {
        $this->authorize('update', $template);

        $updateTemplateAction->execute($template, $request);

        flash()->success(__('Template :template was updated.', ['template' => $template->name]));

        return redirect()->back();
    }

    public function destroy(TransactionalMailTemplate $template)
    {
        $this->authorize('delete', $template);

        $template->delete();

        flash()->success(__('Template :template was deleted.', ['template' => $template->name]));

        return redirect()->route('mailcoach.transactionalMails.templates');
    }

    public function duplicate(TransactionalMailTemplate $template)
    {
        $this->authorize('create', $this->getTransactionalMailTemplateClass());


        /** @var \Spatie\Mailcoach\Domain\Campaign\Models\Template $duplicateTemplate */
        $duplicateTemplate = $template->replicate()->save();

        flash()->success(__('Template :template was duplicated.', ['template' => $template->name]));

        return redirect()->route('mailcoach.transactionalMails.templates.edit', $duplicateTemplate);
    }
}
