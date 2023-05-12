<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Actions;

use Carbon\CarbonInterval;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
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
        return ActionCategoryEnum::pause();
    }

    public static function getName(): string
    {
        return (string) __('Wait for a duration');
    }

    public static function getComponent(): ?string
    {
        return 'wait-action';
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

    public function shouldContinue(Subscriber $subscriber): bool
    {
        if ($subscriber->pivot->created_at <= now()->sub($this->interval)) {
            return true;
        }

        return false;
    }
}
