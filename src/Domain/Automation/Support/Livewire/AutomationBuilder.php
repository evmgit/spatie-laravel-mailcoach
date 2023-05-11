<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Livewire;

use Illuminate\Support\Str;

class AutomationBuilder extends AutomationActionComponent
{
    public string $name = '';

    public array $actions = [];

    protected $listeners = [
        'actionSaved',
        'actionDeleted',
        'validationFailed',
        'saveActions',
    ];

    public function actionSaved(string $uuid, array $actionData)
    {
        $index = collect($this->actions)->search(function ($action) use ($uuid) {
            return $action['uuid'] === $uuid;
        });

        if ($index === false) {
            return;
        }

        $this->actions[$index]['data'] = $actionData;

        $this->emitUp('automationBuilderUpdated', $this->getData());
    }

    public function actionDeleted(string $uuid)
    {
        $index = collect($this->actions)->search(function ($action) use ($uuid) {
            return $action['uuid'] === $uuid;
        });

        if ($index === false) {
            return;
        }

        unset($this->actions[$index]);

        $this->actions = array_values($this->actions);

        $this->emitUp('automationBuilderUpdated', $this->getData());
    }

    public function addAction(string $actionClass, int $index): void
    {
        $uuid = Str::uuid()->toString();
        $editable = (bool) $actionClass::getComponent();

        array_splice($this->actions, $index, 0, [[
            'uuid' => $uuid,
            'class' => $actionClass,
            'data' => [
                'editing' => $editable,
                'editable' => $editable,
            ],
            'active' => 0,
            'completed' => 0,
        ]]);

        if ($editable) {
            $this->emitUp('editAction', $uuid);
        }

        $this->emitUp('automationBuilderUpdated', $this->getData());
    }

    public function getData(): array
    {
        return [
            'name' => $this->name,
            'actions' => $this->actions,
        ];
    }

    public function render()
    {
        $actionOptions = collect(config('mailcoach.automation.flows.actions'))
            ->groupBy(fn (string $action) => $action::getCategory()->value);

        return view('mailcoach::app.automations.components.automationBuilder', [
            'actionOptions' => $actionOptions,
            'actions' => $this->actions,
        ]);
    }

    public function updated($fieldName): void
    {
        $this->resetValidation($fieldName);

        $this->emitUp('automationBuilderUpdated', $this->getData());
    }
}
