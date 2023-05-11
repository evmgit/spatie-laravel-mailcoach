<?php

namespace Spatie\Mailcoach\Domain\Settings\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Mailcoach\Domain\Shared\Models\HasUuid;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class WebhookConfiguration extends Model
{
    use HasUuid;
    use UsesMailcoachModels;
    use HasFactory;

    public $guarded = [];

    public $table = 'mailcoach_webhook_configurations';

    public $casts = [
        'use_for_all_lists' => 'boolean',
        'secret' => 'encrypted',
    ];

    public function emailLists(): BelongsToMany
    {
        return $this->belongsToMany(
            $this->getEmailListClass(),
            'mailcoach_webhook_configuration_email_lists',
            'webhook_configuration_id',
            'email_list_id',
        );
    }
}
