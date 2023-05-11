<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Actions;

use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder;
use Spatie\Mailcoach\Domain\Automation\Models\Action;
use Spatie\Mailcoach\Domain\Automation\Models\ActionSubscriber;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\Enums\ActionCategoryEnum;

class WaitAction extends AutomationAction
{
    public function __construct(
        public CarbonInterval $interval,
        public ?int $length = null,
        public ?string $unit = null
    ) {
        parent::__construct();
    }

    public static function getCategory(): ActionCategoryEnum
    {
        return ActionCategoryEnum::Pause;
    }

    public static function getName(): string
    {
        return (string) __mc('Wait for a duration');
    }

    public static function getComponent(): ?string
    {
        return 'mailcoach::wait-action';
    }

    public static function make(array $data): self
    {
        if (isset($data['seconds'])) {
            return new self(
                CarbonInterval::create(years: 0, seconds: $data['seconds']),
                $data['length'] ?? null,
                $data['unit'] ?? null
            );
        }

        return new self(
            CarbonInterval::createFromDateString("{$data['length']} {$data['unit']}"),
            $data['length'] ?? null,
            $data['unit'] ?? null
        );
    }

    public function toArray(): array
    {
        if (! isset($this->unit, $this->length)) {
            [$length, $unit] = explode(' ', $this->interval->forHumans());
            $this->length = (int) $length;
            $this->unit = $unit;
        }

        return [
            'seconds' => $this->interval->totalSeconds,
            'unit' => $this->unit,
            'length' => $this->length,
        ];
    }

    public function shouldContinue(ActionSubscriber $actionSubscriber): bool
    {
        if ($actionSubscriber->created_at <= now()->sub($this->interval)) {
            return true;
        }

        return false;
    }

    public function getActionSubscribersQuery(Action $action): Builder|\Illuminate\Database\Eloquent\Builder|Relation
    {
        return $action->pendingActionSubscribers()
            ->where('created_at', '<=', now()->sub($this->interval));
    }
}
