<?php

namespace Spatie\Mailcoach\Domain\TransactionalMail\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Database\Factories\TransactionalMailFactory;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Domain\TransactionalMail\Mails\ResendTransactionalMail;

class TransactionalMail extends Model
{
    use HasFactory;
    use UsesMailcoachModels;

    public $table = 'mailcoach_transactional_mails';

    public $guarded = [];

    public $casts = [
        'from' => 'array',
        'to' => 'array',
        'cc' => 'array',
        'bcc' => 'array',
        'track_opens' => 'boolean',
        'track_clicks' => 'boolean',
    ];

    public function send(): HasOne
    {
        return $this->hasOne($this->getSendClass(), 'transactional_mail_id');
    }

    public function opens(): HasManyThrough
    {
        return $this
            ->hasManyThrough(
                TransactionalMailOpen::class,
                $this->getSendClass(),
                'transactional_mail_id'
            )
            ->orderBy('created_at');
    }

    public function clicks(): HasManyThrough
    {
        return $this
            ->hasManyThrough(
                TransactionalMailClick::class,
                $this->getSendClass(),
                'transactional_mail_id'
            )
            ->orderBy('created_at');
    }

    public function clicksPerUrl(): Collection
    {
        return $this->clicks
            ->groupBy('url')
            ->map(function ($group, $url) {
                return [
                    'url' => $url,
                    'count' => $group->count(),
                    'first_clicked_at' => $group->first()->created_at,
                ];
            })
            ->sortByDesc('count')
            ->values();
    }

    public function resend(): self
    {
        Mail::send(new ResendTransactionalMail($this));

        return $this;
    }

    public function toString(): string
    {
        return collect($this->to)
            ->map(function ($person) {
                return $person['email'];
            })
            ->implode(', ');
    }

    public function resolveRouteBinding($value, $field = null)
    {
        $field ??= $this->getRouteKeyName();

        return $this->getTransactionalMailClass()::where($field, $value)->firstOrFail();
    }

    protected static function newFactory(): TransactionalMailFactory
    {
        return new TransactionalMailFactory();
    }
}
