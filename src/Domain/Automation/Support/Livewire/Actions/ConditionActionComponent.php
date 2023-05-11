<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Livewire\Actions;

use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Spatie\Mailcoach\Domain\Automation\Support\Conditions\Condition;
use Spatie\Mailcoach\Domain\Automation\Support\Conditions\HasClickedAutomationMail;
use Spatie\Mailcoach\Domain\Automation\Support\Conditions\HasOpenedAutomationMail;
use Spatie\Mailcoach\Domain\Automation\Support\Conditions\HasTagCondition;
use Spatie\Mailcoach\Domain\Automation\Support\Livewire\AutomationActionComponent;

class ConditionActionComponent extends AutomationActionComponent
{
    public string $length = '1';

    public string $unit = 'days';

    public array $units = [
        'minutes' => 'Minute',
        'hours' => 'Hour',
        'days' => 'Day',
        'weeks' => 'Week',
        'months' => 'Month',
    ];

    public array $editingActions = [];

    public array $yesActions = [];

    public array $noActions = [];

    public string $condition = '';

    public array $conditionOptions = [];

    public array $conditionData = [];

    protected $listeners = ['automationBuilderUpdated', 'editAction', 'actionSaved', 'actionDeleted'];

    public function getData(): array
    {
        return [
            'length' => (int) $this->length,
            'unit' => $this->unit,
            'condition' => $this->condition,
            'conditionData' => $this->conditionData,
            'yesActions' => $this->yesActions,
            'noActions' => $this->noActions,
        ];
    }

    public function updatedCondition()
    {
        /** @var \Spatie\Mailcoach\Domain\Automation\Support\Conditions\Condition $condition */
        $condition = $this->condition;

        if (! method_exists($condition, 'rules')) {
            return;
        }

        /** @var HasTagCondition|HasOpenedAutomationMail|HasClickedAutomationMail $condition */
        foreach (array_keys($condition::rules()) as $key) {
            if (! isset($this->conditionData[$key])) {
                $this->conditionData[$key] = '';
            }
        }
    }

    public function mount()
    {
        $defaultConditions = collect([
            HasTagCondition::class,
            HasOpenedAutomationMail::class,
            HasClickedAutomationMail::class,
        ]);

        $customConditions = collect(config('mailcoach.automation.flows.conditions', []))
            ->filter(fn ($condition) => in_array(Condition::class, class_implements($condition)));

        $this->conditionOptions = $defaultConditions
            ->merge($customConditions)
            ->mapWithKeys(function ($class) {
                return [$class => $class::getName()];
            })->toArray();

        $this->unit = Str::plural($this->unit);
    }

    public function automationBuilderUpdated(array $data): void
    {
        if (! Str::startsWith($data['name'], $this->uuid)) {
            return;
        }

        if ($data['name'] === $this->uuid.'-yes-actions') {
            $this->yesActions = $data['actions'];
        }

        if ($data['name'] === $this->uuid.'-no-actions') {
            $this->noActions = $data['actions'];
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
        $rules = [
            'length' => ['required', 'integer', 'min:1'],
            'unit' => ['required', Rule::in(array_keys($this->units))],
            'condition' => ['required'],
            'conditionData' => ['required', 'array'],
            'yesActions' => ['nullable', 'array'],
            'noActions' => ['nullable', 'array'],
        ];

        if (! method_exists($this->condition, 'rules')) {
            return $rules;
        }

        $conditionRules = collect($this->condition ? $this->condition::rules() : [])->mapWithKeys(function ($rules, $key) {
            return ["conditionData.{$key}" => $rules];
        })->toArray();

        return array_merge($rules, $conditionRules);
    }

    public function render()
    {
        return view('mailcoach::app.automations.components.actions.conditionAction');
    }
}
