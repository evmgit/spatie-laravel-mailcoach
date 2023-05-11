<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Campaigns;

use Illuminate\Http\Request;
use Spatie\Mailcoach\Http\App\Livewire\DataTableComponent;
use Spatie\Mailcoach\Http\App\Queries\TemplatesQuery;

class TemplatesComponent extends DataTableComponent
{
    public function duplicateTemplate(int $id)
    {
        $template = self::getTemplateClass()::find($id);

        $this->authorize('create', self::getTemplateClass());

        $duplicateTemplate = self::getTemplateClass()::create([
            'name' => __mc('Duplicate of').' '.$template->name,
            'html' => $template->html,
            'structured_html' => $template->structured_html,
        ]);

        flash()->success(__mc('Template :template was duplicated.', ['template' => $template->name]));

        return redirect()->route('mailcoach.templates.edit', $duplicateTemplate);
    }

    public function deleteTemplate(int $id)
    {
        $template = self::getTemplateClass()::find($id);

        $this->authorize('delete', $template);

        $template->delete();

        $this->flash(__mc('Template :template was deleted.', ['template' => $template->name]));
    }

    public function getTitle(): string
    {
        return __mc('Templates');
    }

    public function getView(): string
    {
        return 'mailcoach::app.templates.index';
    }

    public function getData(Request $request): array
    {
        return [
            'templates' => (new TemplatesQuery($request))->paginate($request->per_page),
            'totalTemplatesCount' => self::getTemplateClass()::count(),
        ];
    }
}
