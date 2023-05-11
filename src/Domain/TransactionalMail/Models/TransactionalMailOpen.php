<?php

namespace Spatie\Mailcoach\Domain\TransactionalMail\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Mailcoach\Database\Factories\TransactionalMailOpenFactory;
use Spatie\Mailcoach\Domain\Shared\Models\HasUuid;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class TransactionalMailOpen extends Model
{
    use HasUuid;
    use HasFactory;
    use UsesMailcoachModels;

    public $table = 'mailcoach_transactional_mail_opens';

    protected $guarded = [];

    public function send(): BelongsTo
    {
        return $this->belongsTo(self::getSendClass(), 'send_id');
    }

    protected static function newFactory(): TransactionalMailOpenFactory
    {
        return new TransactionalMailOpenFactory();
    }
}
