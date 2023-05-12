<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Livewire\Actions;

use Illuminate\Support\Str;
use Spatie\Mailcoach\Domain\Automation\Support\Livewire\AutomationActionComponent;

class SplitActionComponent extends AutomationActionComponent
{
    public array $editingActions = [];

    public array $leftActions = [];

    public array $rightActions = [];

    protected $listeners = ['automationBuilderUpdated', 'editAction', 'actionSaved', 'actionDeleted'];

    public function getData(): array
    {
        return [
            'leftActions' => $this->leftActions,
            'rightActions' => $this->rightActions,
        ];
    }

    public function automationBuilderUpdated(array $data): void
    {
        if (! Str::startsWith($data['name'], $this->uuid)) {
            return;
        }

        if ($data['name'] === $this->uuid . '-left-actions') {
            $this->leftActions = $data['actions'];
        }

        if ($data['name'] === $this->uuid . '-right-actions') {
            $this->rightActions = $data['actions'];
        }

        $this->emitUp('actionUpdated', $this->getData());
    }

    public function editAction(string $uuid)
    {
        $this->editingActions[] = $uuid;
    }

    public function actionSaved(string $uuid)
    {
        $actions = array_filter($this->editingActions, function ($actionUuid) use ($uuid) {
            return $actionUuid !== $uuid;
        });

        $this->editingActions = $actions;
    }

    public function actionDeleted(string $uuid)
    {
        $actions = array_filter($this->editingActions, function ($actionUuid) use ($uuid) {
            return $actionUuid !== $uuid;
        });

        $this->editingActions = $actions;
    }

    public function rules(): array
    {
        return [
            'leftActions' => ['nullable', 'array'],
            'rightActions' => ['nullable', 'array'],
        ];
    }

    public function render()
    {
        return view('mailcoach::app.automations.components.actions.splitAction');
    }
}
