<?php

namespace Spatie\Mailcoach\Http\App\Requests\Campaigns;

use Carbon\CarbonInterface;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\Mailcoach\Domain\Campaign\Rules\DateTimeFieldRule;

class ScheduleCampaignRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'scheduled_at' => ['required', new DateTimeFieldRule()],
        ];
    }

    public function getScheduledAt(): CarbonInterface
    {
        return (new DateTimeFieldRule())->parseDateTime($this->scheduled_at);
    }
}
