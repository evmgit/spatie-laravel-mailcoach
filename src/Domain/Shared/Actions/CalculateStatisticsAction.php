<?php

namespace Spatie\Mailcoach\Domain\Shared\Actions;

use Spatie\Mailcoach\Domain\Automation\Events\AutomationMailStatisticsCalculatedEvent;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMailLink;
use Spatie\Mailcoach\Domain\Campaign\Events\CampaignStatisticsCalculatedEvent;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Campaign\Models\CampaignLink;
use Spatie\Mailcoach\Domain\Shared\Models\Sendable;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class CalculateStatisticsAction
{
    use UsesMailcoachModels;

    public function execute(Sendable $sendable): void
    {
        if ($sendable->sends()->count() > 0) {
            $this
                ->calculateStatistics($sendable)
                ->calculateLinkStatistics($sendable);
        }

        $sendable->update(['statistics_calculated_at' => now()]);

        match ($sendable::class) {
            static::getCampaignClass() => event(new CampaignStatisticsCalculatedEvent($sendable)),
            static::getAutomationMailClass() => event(new AutomationMailStatisticsCalculatedEvent($sendable)),
        };
    }

    protected function calculateStatistics(Sendable $sendable): self
    {
        $sentToNumberOfSubscribers = $sendable->sends()->count();

        [$openCount, $uniqueOpenCount, $openRate] = $this->calculateOpenMetrics($sendable, $sentToNumberOfSubscribers);
        [$clickCount, $uniqueClickCount, $clickRate] = $this->calculateClickMetrics($sendable, $sentToNumberOfSubscribers);
        [$unsubscribeCount, $unsubscribeRate] = $this->calculateUnsubscribeMetrics($sendable, $sentToNumberOfSubscribers);
        [$bounceCount, $bounceRate] = $this->calculateBounceMetrics($sendable, $sentToNumberOfSubscribers);

        $sendable->update([
            'sent_to_number_of_subscribers' => $sentToNumberOfSubscribers,
            'open_count' => $openCount,
            'unique_open_count' => $uniqueOpenCount,
            'open_rate' => $openRate,
            'click_count' => $clickCount,
            'unique_click_count' => $uniqueClickCount,
            'click_rate' => $clickRate,
            'unsubscribe_count' => $unsubscribeCount,
            'unsubscribe_rate' => $unsubscribeRate,
            'bounce_count' => $bounceCount,
            'bounce_rate' => $bounceRate,
        ]);

        return $this;
    }

    protected function calculateLinkStatistics(Sendable $sendable): self
    {
        $sendable->links()->each(function (CampaignLink | AutomationMailLink $link) {
            $tableName = $link instanceof CampaignLink
                ? static::getCampaignClickTableName()
                : static::getAutomationMailClickTableName();

            $link->update([
                'click_count' => $link->clicks()->count(),
                'unique_click_count' => $link->clicks()->select("{$tableName}.subscriber_id")->groupBy("{$tableName}.subscriber_id")->toBase()->select("{$tableName}.subscriber_id")->getCountForPagination(['subscriber_id']),
            ]);
        });

        return $this;
    }

    protected function calculateClickMetrics(Sendable $sendable, int $sendToNumberOfSubscribers): array
    {
        $tableName = $sendable instanceof Campaign
            ? static::getCampaignClickTableName()
            : static::getAutomationMailClickTableName();

        $clickCount = $sendable->clicks()->count();
        $uniqueClickCount = $sendable->clicks()->groupBy("{$tableName}.subscriber_id")->toBase()->select("{$tableName}.subscriber_id")->getCountForPagination(['subscriber_id']);
        $clickRate = round($uniqueClickCount / $sendToNumberOfSubscribers, 4) * 10000;

        return [$clickCount, $uniqueClickCount, $clickRate];
    }

    protected function calculateOpenMetrics(Sendable $sendable, int $sendToNumberOfSubscribers): array
    {
        $tableName = $sendable instanceof Campaign
            ? static::getCampaignOpenTableName()
            : static::getAutomationMailOpenTableName();

        $openCount = $sendable->opens()->count();
        $uniqueOpenCount = $sendable->opens()->groupBy("{$tableName}.subscriber_id")->toBase()->select("{$tableName}.subscriber_id")->getCountForPagination(['subscriber_id']);
        $openRate = round($uniqueOpenCount / $sendToNumberOfSubscribers, 4) * 10000;

        return [$openCount, $uniqueOpenCount, $openRate];
    }

    protected function calculateUnsubscribeMetrics(Sendable $sendable, int $sendToNumberOfSubscribers): array
    {
        $unsubscribeCount = $sendable->unsubscribes()->count();
        $unsubscribeRate = round($unsubscribeCount / $sendToNumberOfSubscribers, 4) * 10000;

        return [$unsubscribeCount, $unsubscribeRate];
    }

    protected function calculateBounceMetrics(Sendable $sendable, int $sendToNumberOfSubscribers): array
    {
        $bounceCount = $sendable->bounces()->count();
        $bounceRate = round($bounceCount / $sendToNumberOfSubscribers, 4) * 10000;

        return [$bounceCount, $bounceRate];
    }
}
