<?php

namespace Spatie\Mailcoach\Domain\Campaign\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class InactiveSubscriber extends Model
{
    use UsesMailcoachModels;

    public $table = 'mailcoach_inactive_subscribers';

    public $casts = [
        'unsubscribe_at' => 'datetime',
    ];

    protected $guarded = [];

    public function subscriber(): BelongsTo
    {
        return $this->belongsTo(self::getSubscriberClass(), 'subscriber_id');
    }
}
