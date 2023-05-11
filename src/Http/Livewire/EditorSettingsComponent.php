<?php

namespace Spatie\Mailcoach\Http\Livewire;

use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Settings\Support\EditorConfiguration\EditorConfiguration;
use Spatie\Mailcoach\Domain\Settings\Support\EditorConfiguration\EditorConfigurationDriverRepository;
use Spatie\Mailcoach\Http\App\Livewire\LivewireFlash;

class EditorSettingsComponent extends Component
{
    use LivewireFlash;

    public string $contentEditor;

    public string $templateEditor;

    public array $editorSettings = [];

    public array $contentEditorOptions = [];

    public array $templateEditorOptions = [];

    public function mount(EditorConfiguration $editorConfiguration)
    {
        $editorConfigurationDriverRepository = new EditorConfigurationDriverRepository();

        $this->contentEditor = $editorConfiguration->get('contentEditor', $editorConfigurationDriverRepository->getForClass(config('mailcoach.content_editor'))::label());
        $this->templateEditor = $editorConfiguration->get('templateEditor', $editorConfigurationDriverRepository->getForClass(config('mailcoach.template_editor'))::label());

        $this->contentEditorOptions = $editorConfiguration->getContentEditorOptions();
        $this->templateEditorOptions = $editorConfiguration->getTemplateEditorOptions();
    }

    public function rules()
    {
        $editorConfigurationDriverRepository = new EditorConfigurationDriverRepository();

        return array_merge(
            [
                'contentEditor' => ['required', 'bail',  Rule::in($editorConfigurationDriverRepository->getSupportedEditors()->map->label())],
                'templateEditor' => ['required', 'bail',  Rule::in($editorConfigurationDriverRepository->getSupportedEditors()->map->label())],
            ],
            $this->getEditorSpecificValidationRules('contentEditor', $editorConfigurationDriverRepository),
            $this->getEditorSpecificValidationRules('templateEditor', $editorConfigurationDriverRepository),
        );
    }

    private function getEditorSpecificValidationRules(string $property, EditorConfigurationDriverRepository $editorConfigurationDriverRepository): array
    {
        if (! $editor = $editorConfigurationDriverRepository->getForEditor($this->$property ?? '')) {
            return [];
        }

        return collect($editor->validationRules())->mapWithKeys(function ($rules, $key) {
            return ['editorSettings.'.$key => $rules];
        })->toArray();
    }

    public function save()
    {
        $data = $this->validate();

        $data = array_merge(
            Arr::except($data, 'editorSettings'),
            $data['editorSettings'] ?? [],
        );

        resolve(EditorConfiguration::class)->put($data);

        flash()->success(__mc('The editor has been updated.'));

        return redirect(request()->header('Referer'));
    }

    public function render()
    {
        $editorConfiguration = resolve(EditorConfiguration::class);
        $editorConfigurationDriverRepository = new EditorConfigurationDriverRepository();
        $contentConfigurationDriver = $editorConfigurationDriverRepository->getForEditor($this->contentEditor);
        $templateConfigurationDriver = $editorConfigurationDriverRepository->getForEditor($this->templateEditor);

        $currentValues = $this->editorSettings;

        $this->editorSettings = [];
        $this->editorSettings = array_merge($this->editorSettings, $contentConfigurationDriver->defaults());
        $this->editorSettings = array_merge($this->editorSettings, $templateConfigurationDriver->defaults());
        $this->editorSettings = array_merge($this->editorSettings, $editorConfiguration->all());
        $this->editorSettings = array_merge($this->editorSettings, $currentValues);

        return view('mailcoach::app.configuration.editor.edit')
            ->layout('mailcoach::app.layouts.settings', ['title' => __mc('Editor')]);
    }
}
