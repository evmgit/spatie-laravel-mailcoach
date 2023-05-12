<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Livewire\Components;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Automation\Models\Action;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class AutomationActionsFormComponent extends Component
{
    use UsesMailcoachModels;

    protected $listeners = [
        'automationBuilderUpdated',
        'editAction',
        'actionSaved',
        'actionDeleted',
    ];

    public Automation $automation;

    public array $editingActions = [];

    public array $actions = [];

    public bool $unsavedChanges = false;

    public function mount()
    {
        $this->actions = $this->automation->actions()
            ->withCount(['completedSubscribers', 'activeSubscribers'])
            ->get()
            ->map(function (Action $action) {
                try {
                    return $action->toLivewireArray();
                } catch (ModelNotFoundException) {
                    return null;
                }
            })
            ->filter()
            ->values()
            ->toArray();
    }

    public function editAction(string $uuid)
    {
        $this->editingActions[] = $uuid;
        $this->unsavedChanges = true;
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

    public function render()
    {
        return view('mailcoach::app.automations.partials.actionsForm');
    }

    public function automationBuilderUpdated($data)
    {
        if ($data['name'] !== 'default') {
            return;
        }

        $this->actions = $data['actions'];
    }
}
