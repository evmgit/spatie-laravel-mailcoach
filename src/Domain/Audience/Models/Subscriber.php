<?php

namespace Spatie\Mailcoach\Domain\Audience\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Spatie\Mailcoach\Database\Factories\SubscriberFactory;
use Spatie\Mailcoach\Domain\Audience\Actions\Subscribers\ConfirmSubscriberAction;
use Spatie\Mailcoach\Domain\Audience\Enums\SubscriptionStatus;
use Spatie\Mailcoach\Domain\Audience\Events\TagAddedEvent;
use Spatie\Mailcoach\Domain\Audience\Events\TagRemovedEvent;
use Spatie\Mailcoach\Domain\Audience\Events\UnsubscribedEvent;
use Spatie\Mailcoach\Domain\Audience\Support\PendingSubscriber;
use Spatie\Mailcoach\Domain\Automation\Models\Action;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Campaign\Enums\TagType;
use Spatie\Mailcoach\Domain\Campaign\Models\Concerns\HasExtraAttributes;
use Spatie\Mailcoach\Domain\Campaign\Models\Concerns\HasUuid;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Domain\Shared\Support\Config;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class Subscriber extends Model
{
    use HasUuid;
    use HasExtraAttributes;
    use UsesMailcoachModels;
    use HasFactory;

    public $table = 'mailcoach_subscribers';

    public $casts = [
        'extra_attributes' => 'array',
        'subscribed_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
    ];

    protected $guarded = [];

    public static function createWithEmail(string $email, array $attributes = []): PendingSubscriber
    {
        return new PendingSubscriber($email, $attributes);
    }

    public static function findForEmail(string $email, EmailList $emailList): ?Subscriber
    {
        return static::where('email', $email)
            ->where('email_list_id', $emailList->id)
            ->first();
    }

    public function emailList(): BelongsTo
    {
        return $this->belongsTo(config('mailcoach.models.email_list'), 'email_list_id');
    }

    public function sends(): HasMany
    {
        return $this->hasMany($this->getSendClass(), 'subscriber_id');
    }

    public function opens(): HasMany
    {
        return $this->hasMany(static::getCampaignOpenClass(), 'subscriber_id');
    }

    public function clicks(): HasMany
    {
        return $this->hasMany(static::getCampaignClickClass(), 'subscriber_id');
    }

    public function uniqueClicks(): HasMany
    {
        return $this->clicks()->groupBy('campaign_link_id')->addSelect('campaign_link_id');
    }

    public function tags(): BelongsToMany
    {
        return $this
            ->belongsToMany(Tag::class, 'mailcoach_email_list_subscriber_tags', 'subscriber_id', 'tag_id')
            ->orderBy('name');
    }

    public function actions(): BelongsToMany
    {
        return $this->belongsToMany(self::getAutomationActionClass(), self::getActionSubscriberTableName())
            ->withPivot(['completed_at', 'halted_at', 'run_at'])
            ->withTimestamps();
    }

    public function currentAction(Automation $automation): ?Action
    {
        return $this->currentActions($automation)->first();
    }

    public function currentActions(Automation $automation): Collection
    {
        return $this->actions()
            ->where('automation_id', $automation->id)
            ->wherePivotNull('completed_at')
            ->latest()
            ->get();
    }

    public function unsubscribe(Send $send = null)
    {
        $this->update(['unsubscribed_at' => now()]);

        if ($send) {
            if ($send->campaign_id) {
                static::getCampaignUnsubscribeClass()::firstOrCreate([
                    'campaign_id' => $send->campaign->id,
                    'subscriber_id' => $send->subscriber->id,
                ]);

                $send->campaign->dispatchCalculateStatistics();
            }

            if ($send->automation_mail_id) {
                static::getAutomationMailUnsubscribeClass()::firstOrCreate([
                    'automation_mail_id' => $send->automationMail->id,
                    'subscriber_id' => $send->subscriber->id,
                ]);

                $send->automationMail->dispatchCalculateStatistics();
            }
        }

        event(new UnsubscribedEvent($this, $send));

        return $this;
    }

    public function unsubscribeUrl(Send $send = null): string
    {
        return url(route('mailcoach.unsubscribe', [$this->uuid, optional($send)->uuid]));
    }

    public function unsubscribeTagUrl(string $tag): string
    {
        return url(route('mailcoach.unsubscribe-tag', [$this->uuid, $tag]));
    }

    public function getStatusAttribute(): string
    {
        if (! is_null($this->unsubscribed_at)) {
            return SubscriptionStatus::UNSUBSCRIBED;
        }

        if (! is_null($this->subscribed_at)) {
            return SubscriptionStatus::SUBSCRIBED;
        }

        return SubscriptionStatus::UNCONFIRMED;
    }

    public function confirm()
    {
        $action = Config::getAudienceActionClass('confirm_subscriber', ConfirmSubscriberAction::class);

        return $action->execute($this);
    }

    public function scopeUnconfirmed(Builder $query)
    {
        $query
            ->whereNull('subscribed_at')
            ->whereNull('unsubscribed_at');
    }

    public function scopeSubscribed(Builder $query)
    {
        $query
            ->whereNotNull('subscribed_at')
            ->whereNull('unsubscribed_at');
    }

    public function scopeUnsubscribed(Builder $query)
    {
        $query
            ->whereNotNull('unsubscribed_at');
    }

    public function addTag(string | iterable $name, string $type = null): self
    {
        $names = Arr::wrap($name);

        return $this->addTags($names, $type);
    }

    public function addTags(array $names, string $type = null)
    {
        foreach ($names as $name) {
            if ($this->hasTag($name)) {
                continue;
            }

            $tag = Tag::firstOrCreate([
                'name' => $name,
                'email_list_id' => $this->emailList->id,
            ], [
                'type' => $type ?? TagType::DEFAULT,
            ]);

            $this->tags()->attach($tag);

            event(new TagAddedEvent($this, $tag));
        }

        return $this;
    }

    public function hasTag(string $name): bool
    {
        return $this->tags()
            ->where('name', $name)
            ->where('email_list_id', $this->emailList->id)
            ->exists();
    }

    public function removeTag(string | array $name): self
    {
        $names = Arr::wrap($name);

        return $this->removeTags($names);
    }

    public function removeTags(array $names)
    {
        $tags = $this->tags()->whereIn('name', $names)->get();

        foreach ($tags as $tag) {
            event(new TagRemovedEvent($this, $tag));
        }

        $this->tags()->detach($tags->pluck('id'));

        return $this;
    }

    public function syncTags(array $names, string $type = 'default')
    {
        $this->addTags($names);

        $this->tags()->where('type', $type)->whereNotIn('name', $names)->each(function ($tag) {
            event(new TagRemovedEvent($this, $tag));
        });

        $this->tags()->detach($this->tags()->where('type', $type)->whereNotIn('name', $names)->pluck('mailcoach_tags.id'));

        return $this;
    }

    public function toExportRow(): array
    {
        return [
            'email' => $this->email,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'tags' => $this->tags()->pluck('name')->implode(";"),
        ];
    }

    public function isUnconfirmed(): bool
    {
        return $this->status === SubscriptionStatus::UNCONFIRMED;
    }

    public function isSubscribed(): bool
    {
        return $this->status === SubscriptionStatus::SUBSCRIBED;
    }

    public function isUnsubscribed(): bool
    {
        return $this->status === SubscriptionStatus::UNSUBSCRIBED;
    }

    public function inAutomation(Automation $automation): bool
    {
        return $this->actions()->where('automation_id', $automation->id)->count() > 0;
    }

    public function resolveRouteBinding($value, $field = null)
    {
        $field ??= $this->getRouteKeyName();

        $subscriber = $this->getSubscriberClass()::where($field, $value)->first();

        if ($subscriber) {
            return $subscriber;
        }

        /** Can also bind uuid */
        return $this->getSubscriberClass()::where('uuid', $value)->firstOrFail();
    }

    protected static function newFactory(): SubscriberFactory
    {
        return new SubscriberFactory();
    }
}
