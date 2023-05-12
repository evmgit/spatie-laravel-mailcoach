<?php

namespace Spatie\Mailcoach\Http\App\ViewModels;

use Illuminate\Support\Collection;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Support\Svg\BezierCurve;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\ViewModels\ViewModel;

class CampaignSummaryViewModel extends ViewModel
{
    use UsesMailcoachModels;

    protected Campaign $campaign;

    protected Collection $stats;

    protected int $limit;

    public int $failedSendsCount = 0;

    public function __construct(Campaign $campaign)
    {
        $this->campaign = $campaign;

        $this->stats = $this->createStats();

        $this->limit = (ceil(max($this->stats->max('opens'), $this->stats->max('clicks')) * 1.1 / 10) * 10) ?: 1;

        $this->failedSendsCount = $this->campaign()->sends()->failed()->count();
    }

    public function campaign(): Campaign
    {
        return $this->campaign;
    }

    public function stats(): Collection
    {
        return $this->stats;
    }

    public function limit(): int
    {
        return $this->limit;
    }

    public function opensPath(): string
    {
        $points = $this->stats
            ->pluck('opens')
            ->map(function (int $opens, int $index) {
                return [$index, 100 - ($opens / $this->limit) * 100];
            })
            ->toArray();

        return (new BezierCurve([[0, 100], ...$points, [24,100]]))->toPath();
    }

    public function clicksPath(): string
    {
        $points = $this->stats
            ->pluck('clicks')
            ->map(function (int $clicks, int $index) {
                return [$index, 100 - ($clicks / $this->limit) * 100];
            })
            ->toArray();

        return (new BezierCurve([[0, 100], ...$points, [24,100]]))->toPath();
    }

    protected function createStats(): Collection
    {
        if (! $this->campaign->wasAlreadySent()) {
            return collect();
        }

        $start = $this->campaign->sent_at->toImmutable();

        if ($this->campaign->opens()->count() > 0 && $this->campaign->opens()->first()->created_at < $start) {
            $start = $this->campaign->opens()->first()->created_at->toImmutable();
        }

        return Collection::times(24)->map(function (int $number) use ($start) {
            $datetime = $start->addHours($number - 1);

            $campaignOpenTable = static::getCampaignOpenTableName();
            $campaignClickTable = static::getCampaignClickTableName();

            return [
                'label' => $datetime->format('H:i'),
                'opens' => $this->campaign->opens()->whereBetween("{$campaignOpenTable}.created_at", [$datetime, $datetime->addHour()])->count(),
                'clicks' => $this->campaign->clicks()->whereBetween("{$campaignClickTable}.created_at", [$datetime, $datetime->addHour()])->count(),
            ];
        });
    }
}
