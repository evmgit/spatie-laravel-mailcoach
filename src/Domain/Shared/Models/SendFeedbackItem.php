<?php

namespace Spatie\Mailcoach\Domain\Shared\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Mailcoach\Domain\Campaign\Enums\SendFeedbackType;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class SendFeedbackItem extends Model
{
    use HasUuid;
    use UsesMailcoachModels;

    public $table = 'mailcoach_send_feedback_items';

    protected $guarded = [];

    protected $casts = [
        'send_feedback_id' => 'int',
        'type' => SendFeedbackType::class,
    ];

    public function send(): BelongsTo
    {
        return $this->belongsTo(self::getSendClass());
    }

    public function getFormattedTypeAttribute(): string
    {
        $formattedTypes = [
            SendFeedbackType::Bounce->value => __mc('Bounced'),
            SendFeedbackType::Complaint->value => __mc('Received complaint'),
        ];

        return (string) ($formattedTypes[$this->type->value] ?? '');
    }
}
