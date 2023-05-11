<?php

namespace Spatie\Mailcoach\Domain\Audience\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Mailcoach\Database\Factories\SubscriberImportFactory;
use Spatie\Mailcoach\Domain\Audience\Enums\SubscriberImportStatus;
use Spatie\Mailcoach\Domain\Audience\Support\ImportSubscriberRow;
use Spatie\Mailcoach\Domain\Shared\Models\HasUuid;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class SubscriberImport extends Model implements HasMedia
{
    use InteractsWithMedia;
    use HasUuid;
    use HasFactory;
    use MassPrunable;
    use UsesMailcoachModels;

    public $table = 'mailcoach_subscriber_imports';

    public $guarded = [];

    protected $casts = [
        'imported_subscribers_count' => 'integer',
        'mailcoach_email_lists_ids' => 'array',
        'replace_tags' => 'boolean',
        'status' => SubscriberImportStatus::class,
        'errors' => 'array',
    ];

    public static function booted()
    {
        static::creating(function (SubscriberImport $subscriberImport) {
            if (empty($subscriberImport->status)) {
                $subscriberImport->status = SubscriberImportStatus::Pending;
            }
        });
    }

    public function addError(string $message, ?ImportSubscriberRow $row = null): void
    {
        $values = $row?->getAllValues() ?? [];

        $errors = $this->errors;
        $errors[] = array_merge($values, ['message' => $message]);
        $this->errors = $errors;
        $this->save();
    }

    public function emailList(): BelongsTo
    {
        return $this->belongsTo(self::getEmailListClass(), 'email_list_id');
    }

    public function subscribers(): HasMany
    {
        return $this->hasMany(self::getSubscriberClass(), 'imported_via_import_uuid', 'uuid');
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('importFile')
            ->useDisk(config('mailcoach.audience.import_subscribers_disk'))
            ->singleFile();

        $this
            ->addMediaCollection('importedUsersReport')
            ->useDisk(config('mailcoach.audience.import_subscribers_disk'))
            ->singleFile();

        $this
            ->addMediaCollection('errorReport')
            ->useDisk(config('mailcoach.audience.import_subscribers_disk'))
            ->singleFile();
    }

    protected static function newFactory(): SubscriberImportFactory
    {
        return new SubscriberImportFactory();
    }

    public function prunable()
    {
        return static::where('created_at', '<=', now()->subWeek());
    }
}
