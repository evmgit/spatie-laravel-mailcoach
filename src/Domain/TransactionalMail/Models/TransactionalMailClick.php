<?php

namespace Spatie\Mailcoach\Domain\TransactionalMail\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Mailcoach\Database\Factories\TransactionalMailClickFactory;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class TransactionalMailClick extends Model
{
    use HasFactory;
    use UsesMailcoachModels;

    public $table = 'mailcoach_transactional_mail_clicks';

    protected $guarded = [];

    public function send(): BelongsTo
    {
        return $this->belongsTo($this->getSendClass(), 'send_id');
    }

    protected static function newFactory(): TransactionalMailClickFactory
    {
        return new TransactionalMailClickFactory();
    }
}
