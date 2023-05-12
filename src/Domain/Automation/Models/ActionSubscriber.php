<?php

namespace Spatie\Mailcoach\Domain\Automation\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class ActionSubscriber extends Pivot
{
    use UsesMailcoachModels;

    public $table = 'mailcoach_automation_action_subscriber';

    public $incrementing = true;

    public $timestamps = true;

    protected $casts = [
        'run_at' => 'datetime',
        'completed_at' => 'datetime',
        'halted_at' => 'datetime',
    ];

    public function action(): BelongsTo
    {
        return $this->belongsTo(static::getAutomationActionClass());
    }

    public function subscriber(): BelongsTo
    {
        return $this->belongsTo(static::getSubscriberClass());
    }
}
