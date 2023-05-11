<?php

namespace Spatie\Mailcoach\Http\App\Livewire;

use Carbon\CarbonInterface;
use Illuminate\Support\Arr;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Campaign\Models\Concerns\HasHtmlContent;
use Spatie\Mailcoach\Domain\Campaign\Models\Template;
use Spatie\Mailcoach\Domain\Campaign\Rules\HtmlRule;
use Spatie\Mailcoach\Domain\Shared\Support\TemplateRenderer;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

abstract class EditorComponent extends Component
{
    use UsesMailcoachModels;
    use LivewireFlash;

    public static bool $supportsTemplates = true;

    public static bool $supportsContent = true;

    public HasHtmlContent $model;

    public int|string|null $templateId = null;

    public ?Template $template = null;

    public array $templateFieldValues = [];

    public string $fullHtml = '';

    public string $emails = '';

    public bool $quiet = false;

    public ?CarbonInterface $lastSavedAt = null;

    public bool $autosaveConflict = false;

    public function mount(HasHtmlContent $model)
    {
        $this->model = $model;
        $this->lastSavedAt = $model->updated_at;

        $this->templateFieldValues = $model->getTemplateFieldValues();

        if ($model->hasTemplates()) {
            $this->template = $model->template;
            $this->templateId = $model->template?->id;
        }

        if ($this->template?->containsPlaceHolders()) {
            foreach ($this->template->placeHolderNames() as $placeHolderName) {
                $this->templateFieldValues[$placeHolderName] ??= '';
            }
        } else {
            $this->templateFieldValues['html'] ??= $this->template?->getHtml() ?? '';
        }

        $this->renderFullHtml();
    }

    public function updatingTemplateId(int|string|null $templateId)
    {
        if (! $templateId) {
            $this->template = null;

            return;
        }

        $this->template = self::getTemplateClass()::find($templateId);

        if ($this->template?->containsPlaceHolders()) {
            foreach ($this->template->placeHolderNames() as $placeHolderName) {
                $this->templateFieldValues[$placeHolderName] ??= '';
            }
        } else {
            $this->templateFieldValues['html'] ??= $this->template?->getHtml() ?? '';
        }
    }

    public function updated()
    {
        $this->renderFullHtml();
    }

    public function renderFullHtml()
    {
        if (! $this->template) {
            $html = $this->templateFieldValues['html'] ?? '';
            if (is_array($html)) {
                $html = $html['html'] ?? '';
            }

            $this->fullHtml = $html;

            return;
        }

        $templateRenderer = (new TemplateRenderer($this->template?->html ?? ''));
        $this->fullHtml = $templateRenderer->render($this->templateFieldValues);
    }

    public function rules(): array
    {
        $fieldValues = $this->filterNeededFields($this->templateFieldValues, $this->template);

        return collect($fieldValues)->mapWithKeys(function ($value, $key) {
            return ["templateFieldValues.{$key}" => ['required', new HtmlRule()]];
        })->toArray();
    }

    public function autosave()
    {
        if ($this->lastSavedAt && $this->lastSavedAt->timestamp !== $this->model->fresh()->updated_at->timestamp) {
            $this->autosaveConflict = true;

            return;
        }

        $this->saveQuietly();
    }

    public function saveQuietly()
    {
        $fieldValues = $this->filterNeededFields($this->templateFieldValues, $this->template);

        if ($this->model->hasTemplates()) {
            $this->model->template_id = $this->template?->id;

            if (isset($this->model->attributes['last_modified_at'])) {
                $this->model->last_modified_at = now();
            }
        }

        if (! empty($this->rules)) {
            $this->validate($this->rules());
        }

        $this->model->setHtml($this->fullHtml);
        $this->model->setTemplateFieldValues($fieldValues);
        $this->model->save();
        $this->lastSavedAt = $this->model->updated_at;
        $this->autosaveConflict = false;
    }

    public function save()
    {
        $this->saveQuietly();

        if (! $this->quiet) {
            $this->flash(__mc(':name was updated.', ['name' => $this->model->fresh()->name]));
        }

        $this->emit('editorSaved');
    }

    protected function filterNeededFields(array $fields, ?Template $template): array
    {
        if (! $template) {
            return Arr::only($fields, 'html');
        }

        if (! $template->containsPlaceHolders()) {
            return Arr::only($fields, 'html');
        }

        return Arr::only($fields, Arr::pluck($template->fields(), 'name'));
    }

    abstract public function render();
}
