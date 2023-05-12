<?php

namespace Spatie\Mailcoach\Domain\Automation\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Mailcoach\Database\Factories\AutomationMailOpenFactory;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class AutomationMailOpen extends Model
{
    use HasFactory;
    use UsesMailcoachModels;

    public $table = 'mailcoach_automation_mail_opens';

    protected $guarded = [];

    protected $casts = [
        'first_opened_at' => 'datetime',
    ];

    public function send(): BelongsTo
    {
        return $this->belongsTo($this->getSendClass(), 'send_id');
    }

    protected static function newFactory(): AutomationMailOpenFactory
    {
        return new AutomationMailOpenFactory();
    }
}
