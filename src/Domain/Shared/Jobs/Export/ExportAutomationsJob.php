<?php

namespace Spatie\Mailcoach\Domain\Shared\Jobs\Export;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ExportAutomationsJob extends ExportJob
{
    /**
     * @param  string  $path
     * @param  array<int>  $selectedAutomations
     */
    public function __construct(protected string $path, protected array $selectedAutomations)
    {
    }

    public function name(): string
    {
        return 'Automations';
    }

    public function execute(): void
    {
        $automations = DB::table(self::getAutomationTableName())
            ->select(
                self::getAutomationTableName().'.*',
                DB::raw(self::getEmailListTableName().'.uuid as email_list_uuid'),
                DB::raw(self::getTagSegmentTableName().'.name as segment_name'),
            )
            ->join(self::getEmailListTableName(), self::getEmailListTableName().'.id', '=', self::getAutomationTableName().'.email_list_id')
            ->leftJoin(self::getTagSegmentTableName(), self::getTagSegmentTableName().'.id', '=', self::getAutomationTableName().'.segment_id')
            ->whereIn(self::getAutomationTableName().'.id', $this->selectedAutomations)
            ->get();

        $this->writeFile('automations.csv', $automations);
        $this->addMeta('automations_count', $automations->count());

        $triggers = DB::table(self::getAutomationTriggerTableName())
            ->whereIn('automation_id', $this->selectedAutomations)
            ->get();

        $this->writeFile('automation_triggers.csv', $triggers);
        $this->addMeta('automation_triggers_count', $triggers->count());

        $actions = DB::table(self::getAutomationActionTableName())
            ->whereIn('automation_id', $this->selectedAutomations)
            ->get();

        $this->writeFile('automation_actions.csv', $actions);
        $this->addMeta('automation_actions_count', $actions->count());

        $actionSubscribersCount = 0;
        DB::table(self::getActionSubscriberTableName())
            ->orderBy('id')
            ->select(
                self::getActionSubscriberTableName().'.*',
                DB::raw(self::getSubscriberTableName().'.uuid as subscriber_uuid'),
                DB::raw(self::getAutomationActionTableName().'.uuid as action_uuid'),
            )
            ->join(self::getSubscriberTableName(), self::getSubscriberTableName().'.id', '=', self::getActionSubscriberTableName().'.subscriber_id')
            ->join(
                self::getAutomationActionTableName(),
                self::getAutomationActionTableName().'.id',
                '=',
                self::getActionSubscriberTableName().'.action_id'
            )
            ->whereIn('automation_id', $this->selectedAutomations)
            ->chunk(50_000, function (Collection $actionSubscribers, $index) use (&$actionSubscribersCount) {
                $actionSubscribersCount += $actionSubscribers->count();

                $this->writeFile("automation_action_subscribers-{$index}.csv", $actionSubscribers);
            });

        $this->addMeta('automation_action_subscribers_count', $actionSubscribersCount);
    }
}
