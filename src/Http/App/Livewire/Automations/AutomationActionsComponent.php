<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Automations;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Automation\Models\Action;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\App\Livewire\LivewireFlash;
use Spatie\Mailcoach\MainNavigation;

class AutomationActionsComponent extends Component
{
    use UsesMailcoachModels;
    use LivewireFlash;

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
            ->withCount(['completedSubscribers', 'activeSubscribers', 'haltedSubscribers'])
            ->get()
            ->map(function (Action $action) {
                try {
                    return $action->toLivewireArray();
                } catch (ModelNotFoundException) {
                    $action->delete();

                    return null;
                }
            })
            ->filter()
            ->values()
            ->toArray();

        app(MainNavigation::class)->activeSection()?->add($this->automation->name, route('mailcoach.automations'));
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
        $this->unsavedChanges = true;
    }

    public function save()
    {
        $this->automation->chain($this->actions);

        $this->flash(__mc('Actions successfully saved to automation :automation.', [
            'automation' => $this->automation->name,
        ]));
    }

    public function render()
    {
        return view('mailcoach::app.automations.actions')
            ->layout('mailcoach::app.automations.layouts.automation', [
                'automation' => $this->automation,
                'title' => __mc('Actions'),
            ]);
    }

    public function automationBuilderUpdated($data)
    {
        if ($data['name'] !== 'default') {
            return;
        }

        $this->actions = $data['actions'];
    }
}
