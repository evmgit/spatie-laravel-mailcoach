<?php

namespace Spatie\Mailcoach\Domain\Automation\Support\Triggers;

use Carbon\CarbonInterface;
use Spatie\Mailcoach\Domain\Campaign\Rules\DateTimeFieldRule;

class DateTrigger extends AutomationTrigger implements TriggeredBySchedule
{
    public CarbonInterface $date;

    public function __construct(CarbonInterface $date)
    {
        parent::__construct();

        $this->date = $date;
    }

    public static function getName(): string
    {
        return (string) __mc('On a date');
    }

    public static function getComponent(): ?string
    {
        return 'mailcoach::date-trigger';
    }

    public static function make(array $data): self
    {
        return new self((new DateTimeFieldRule())->parseDateTime($data['date']));
    }

    public static function rules(): array
    {
        return [
            'date' => ['required', new DateTimeFieldRule()],
        ];
    }

    public function trigger(): void
    {
        if (! now()->startOfMinute()->equalTo($this->date->startOfMinute())) {
            return;
        }

        $this->runAutomation($this->automation->newSubscribersQuery());
    }
}
