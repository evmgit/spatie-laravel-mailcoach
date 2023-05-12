<?php

namespace Spatie\Mailcoach\Domain\Shared\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Mailcoach\Domain\Campaign\Enums\SendFeedbackType;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class SendFeedbackItem extends Model
{
    use UsesMailcoachModels;

    public $table = 'mailcoach_send_feedback_items';

    protected $guarded = [];

    public function send(): BelongsTo
    {
        return $this->belongsTo($this->getSendClass());
    }

    public function getFormattedTypeAttribute(): string
    {
        $formattedTypes = [
            SendFeedbackType::BOUNCE => __('Bounced'),
            SendFeedbackType::COMPLAINT => __('Received complaint'),
        ];

        return (string)$formattedTypes[$this->type] ?? '';
    }
}
