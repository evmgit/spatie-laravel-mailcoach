<?php

namespace Spatie\Mailcoach\Domain\Shared\Jobs\Import;

use Illuminate\Support\Arr;
use Spatie\Mailcoach\Domain\Automation\Enums\AutomationStatus;
use Spatie\SimpleExcel\SimpleExcelReader;

class ImportAutomationsJob extends ImportJob
{
    private int $index = 0;

    private int $total = 0;

    /** @var array<string, int> */
    private array $emailLists = [];

    /** @var array<int, int> */
    private array $automationMapping = [];

    /** @var array<int, int> */
    private array $automationActionMapping = [];

    public function name(): string
    {
        return 'Automations';
    }

    public function execute(): void
    {
        $this->total = $this->getMeta('automations_count', 0) + $this->getMeta('automation_triggers_count', 0) + $this->getMeta('automation_actions_count', 0);
        $this->emailLists = self::getEmailListClass()::pluck('id', 'uuid')->toArray();

        $this->importAutomations();
        $this->importAutomationTriggers();
        $this->importAutomationActions();
    }

    private function importAutomations(): void
    {
        if (! $this->importDisk->exists('import/automations.csv')) {
            return;
        }

        $this->tmpDisk->writeStream('tmp/automations.csv', $this->importDisk->readStream('import/automations.csv'));

        $reader = SimpleExcelReader::create($this->tmpDisk->path('tmp/automations.csv'));

        foreach ($reader->getRows() as $row) {
            $row['email_list_id'] = $this->emailLists[$row['email_list_uuid']];
            $row['segment_id'] = self::getTagSegmentClass()::where('name', $row['segment_name'])->where('email_list_id', $row['email_list_id'])->first()?->id;
            $row['status'] = AutomationStatus::Paused;

            $automation = self::getAutomationClass()::firstOrCreate(
                ['uuid' => $row['uuid']],
                array_filter(Arr::except($row, ['id', 'email_list_uuid', 'segment_name'])),
            );
            $this->automationMapping[$row['id']] = $automation->id;

            $this->index++;
            $this->updateJobProgress($this->index, $this->total);
        }

        $this->tmpDisk->delete('tmp/automations.csv');
    }

    private function importAutomationTriggers(): void
    {
        if (! $this->importDisk->exists('import/automation_triggers.csv')) {
            return;
        }

        $this->tmpDisk->writeStream('tmp/automation_triggers.csv', $this->importDisk->readStream('import/automation_triggers.csv'));

        $reader = SimpleExcelReader::create($this->tmpDisk->path('tmp/automation_triggers.csv'));

        foreach ($reader->getRows() as $row) {
            $row['automation_id'] = $this->automationMapping[$row['automation_id']];

            $triggerClass = self::getAutomationTriggerClass();

            if (! $triggerClass::where('uuid', $row['uuid'])->exists()) {
                $trigger = new $triggerClass;
                $trigger->setRawAttributes(Arr::except($row, ['id']));
                $trigger->save();
            }

            $this->index++;
            $this->updateJobProgress($this->index, $this->total);
        }

        $this->tmpDisk->delete('tmp/automation_triggers.csv');
    }

    private function importAutomationActions(): void
    {
        if (! $this->importDisk->exists('import/automation_actions.csv')) {
            return;
        }

        $this->tmpDisk->writeStream('tmp/automation_actions.csv', $this->importDisk->readStream('import/automation_actions.csv'));

        $reader = SimpleExcelReader::create($this->tmpDisk->path('tmp/automation_actions.csv'));

        foreach ($reader->getRows() as $row) {
            $row['automation_id'] = $this->automationMapping[$row['automation_id']];
            $row['parent_id'] = $row['parent_id']
                ? $this->automationActionMapping[$row['automation_id']] ?? null
                : null;

            $actionClass = self::getAutomationActionClass();

            if (! $action = $actionClass::where('uuid', $row['uuid'])->first()) {
                $action = new $actionClass;
                $action->setRawAttributes(Arr::except($row, ['id']));
                $action->save();
            }

            $this->automationActionMapping[$row['id']] = $action->id;

            $this->index++;
            $this->updateJobProgress($this->index, $this->total);
        }

        $this->tmpDisk->delete('tmp/automation_actions.csv');
    }
}
