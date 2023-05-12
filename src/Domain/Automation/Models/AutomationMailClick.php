<?php

namespace Spatie\Mailcoach\Domain\Automation\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Mailcoach\Database\Factories\AutomationMailClickFactory;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class AutomationMailClick extends Model
{
    use HasFactory;
    use UsesMailcoachModels;

    public $table = 'mailcoach_automation_mail_clicks';

    protected $guarded = [];

    public function send(): BelongsTo
    {
        return $this->belongsTo($this->getSendClass(), 'send_id');
    }

    public function link(): BelongsTo
    {
        return $this->belongsTo(static::getAutomationMailLinkClass(), 'automation_mail_link_id');
    }

    protected static function newFactory(): AutomationMailClickFactory
    {
        return new AutomationMailClickFactory();
    }
}
