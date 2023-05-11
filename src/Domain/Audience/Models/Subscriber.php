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
use Illuminate\Support\Str;
use ParagonIE\CipherSweet\BlindIndex;
use ParagonIE\CipherSweet\CipherSweet as CipherSweetEngine;
use ParagonIE\CipherSweet\EncryptedRow;
use Spatie\LaravelCipherSweet\Concerns\UsesCipherSweet;
use Spatie\LaravelCipherSweet\Contracts\CipherSweetEncrypted;
use Spatie\LaravelCipherSweet\Observers\ModelObserver;
use Spatie\Mailcoach\Database\Factories\SubscriberFactory;
use Spatie\Mailcoach\Domain\Audience\Actions\Subscribers\ConfirmSubscriberAction;
use Spatie\Mailcoach\Domain\Audience\Encryption\Transformation\EmailFirstPart;
use Spatie\Mailcoach\Domain\Audience\Encryption\Transformation\EmailSecondPart;
use Spatie\Mailcoach\Domain\Audience\Encryption\Transformation\Lowercase;
use Spatie\Mailcoach\Domain\Audience\Enums\SubscriptionStatus;
use Spatie\Mailcoach\Domain\Audience\Events\TagAddedEvent;
use Spatie\Mailcoach\Domain\Audience\Events\TagRemovedEvent;
use Spatie\Mailcoach\Domain\Audience\Events\UnsubscribedEvent;
use Spatie\Mailcoach\Domain\Audience\Support\PendingSubscriber;
use Spatie\Mailcoach\Domain\Automation\Models\Action;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Campaign\Enums\TagType;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Campaign\Models\Concerns\HasExtraAttributes;
use Spatie\Mailcoach\Domain\Shared\Models\HasUuid;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Domain\Shared\Traits\Searchable;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Mailcoach;

class Subscriber extends Model implements CipherSweetEncrypted
{
    use HasUuid;
    use HasExtraAttributes;
    use UsesMailcoachModels;
    use HasFactory;
    use UsesCipherSweet;
    use Searchable;

    public $table = 'mailcoach_subscribers';

    protected $guarded = [];

    public $casts = [
        'extra_attributes' => 'array',
        'subscribed_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
    ];

    protected function getSearchableConfig(): array
    {
        return [
            'columns' => [
                self::getSubscriberTableName().'.email' => 15,
                self::getSubscriberTableName().'.first_name' => 10,
                self::getSubscriberTableName().'.last_name' => 10,
                self::getTagTableName().'.name' => 5,
            ],
            'joins' => [
                'mailcoach_email_list_subscriber_tags' => [self::getSubscriberTableName().'.id', 'mailcoach_email_list_subscriber_tags.subscriber_id'],
                self::getTagTableName() => ['mailcoach_email_list_subscriber_tags.tag_id', self::getTagTableName().'.id'],
            ],
        ];
    }

    protected static function bootUsesCipherSweet()
    {
        if (! config('mailcoach.encryption.enabled')) {
            return;
        }

        static::observe(ModelObserver::class);

        static::$cipherSweetEncryptedRow = new EncryptedRow(
            app(CipherSweetEngine::class),
            (new static())->getTable()
        );

        static::configureCipherSweet(static::$cipherSweetEncryptedRow);
    }

    public static function configureCipherSweet(EncryptedRow $encryptedRow): void
    {
        $encryptedRow
            ->addTextField('email')
            ->addTextField('first_name')
            ->addTextField('last_name');

        $encryptedRow->addBlindIndex('email', new BlindIndex('email_first_part', [new EmailFirstPart()]));
        $encryptedRow->addBlindIndex('email', new BlindIndex('email_second_part', [new EmailSecondPart()]));

        $encryptedRow->addBlindIndex('first_name', new BlindIndex('first_name', [new Lowercase()]));
        $encryptedRow->addBlindIndex('last_name', new BlindIndex('last_name', [new Lowercase()]));
    }

    public static function createWithEmail(string $email, array $attributes = []): PendingSubscriber
    {
        return new PendingSubscriber($email, $attributes);
    }

    public static function findForEmail(string $email, EmailList $emailList): ?Subscriber
    {
        $query = static::query()->where('email_list_id', $emailList->id);

        if (config('mailcoach.encryption.enabled')) {
            return $query
                ->whereBlind('email', 'email_first_part', $email)
                ->whereBlind('email', 'email_second_part', $email)
                ->first();
        }

        return $query->where('email', $email)->first();
    }

    public function emailList(): BelongsTo
    {
        return $this->belongsTo(self::getEmailListClass(), 'email_list_id');
    }

    public function sends(): HasMany
    {
        return $this->hasMany(self::getSendClass(), 'subscriber_id');
    }

    public function opens(): HasMany
    {
        return $this->hasMany(self::getCampaignOpenClass(), 'subscriber_id');
    }

    public function clicks(): HasMany
    {
        return $this->hasMany(self::getCampaignClickClass(), 'subscriber_id');
    }

    public function uniqueClicks(): HasMany
    {
        return $this->clicks()->groupBy('campaign_link_id')->addSelect('campaign_link_id');
    }

    public function tags(): BelongsToMany
    {
        return $this
            ->belongsToMany(self::getTagClass(), 'mailcoach_email_list_subscriber_tags', 'subscriber_id', 'tag_id')
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

    public function currentActionClass(Automation $automation): ?string
    {
        if (! $action = $this->currentAction($automation)) {
            return null;
        }

        return $action->action::class;
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
                ], ['uuid' => Str::uuid()]);

                $send->campaign->dispatchCalculateStatistics();
            }

            if ($send->automation_mail_id) {
                static::getAutomationMailUnsubscribeClass()::firstOrCreate([
                    'automation_mail_id' => $send->automationMail->id,
                    'subscriber_id' => $send->subscriber->id,
                ], ['uuid' => Str::uuid()]);

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

    public function unsubscribeTagUrl(string $tag, Send $send = null): string
    {
        return url(route('mailcoach.unsubscribe-tag', [$this->uuid, $tag, optional($send)->uuid]));
    }

    public function preferencesUrl(Send $send = null): string
    {
        return url(route('mailcoach.manage-preferences', [$this->uuid, optional($send)->uuid]));
    }

    public function getStatusAttribute(): SubscriptionStatus
    {
        if (! is_null($this->unsubscribed_at)) {
            return SubscriptionStatus::Unsubscribed;
        }

        if (! is_null($this->subscribed_at)) {
            return SubscriptionStatus::Subscribed;
        }

        return SubscriptionStatus::Unconfirmed;
    }

    public function confirm()
    {
        $action = Mailcoach::getAudienceActionClass('confirm_subscriber', ConfirmSubscriberAction::class);

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

    public function scopeWithoutSendsForCampaign(Builder $query, Campaign $campaign)
    {
        return $query->whereDoesntHave('sends', function (Builder $query) use ($campaign) {
            $query->where('campaign_id', $campaign->id);
        });
    }

    public function addTag(string|iterable $name, ?TagType $type = null): self
    {
        $names = Arr::wrap($name);

        return $this->addTags($names, $type);
    }

    public function addTags(array $names, ?TagType $type = null)
    {
        foreach ($names as $name) {
            if ($this->hasTag($name)) {
                continue;
            }

            $tag = self::getTagClass()::firstOrCreate([
                'name' => $name,
                'email_list_id' => $this->emailList->id,
            ], [
                'uuid' => Str::uuid(),
                'type' => $type ?? TagType::Default,
            ]);

            $this->tags()->attach($tag);
            $this->tags->add($tag);

            event(new TagAddedEvent($this, $tag));
        }

        return $this;
    }

    public function hasTag(string $name): bool
    {
        return $this->tags()
            ->where('name', $name)
            ->where('email_list_id', $this->emailList->id)
            ->count() > 0;
    }

    public function removeTag(string|array $name): self
    {
        $names = Arr::wrap($name);

        return $this->removeTags($names);
    }

    public function removeTags(array $names)
    {
        $tags = $this->tags()->whereIn('name', $names)->get();

        if ($tags->isEmpty()) {
            return $this;
        }

        foreach ($tags as $tag) {
            event(new TagRemovedEvent($this, $tag));
        }

        $this->tags()->detach($tags->pluck('id'));

        $this->load('tags');

        return $this;
    }

    public function syncTags(?array $names, string $type = 'default')
    {
        $names ??= [];

        $this->addTags($names);

        $this->tags()->where('type', $type)->whereNotIn('name', $names)->each(function ($tag) {
            event(new TagRemovedEvent($this, $tag));
        });

        $this->tags()->detach($this->tags()->where('type', $type)->whereNotIn('name', $names)->pluck(self::getTagTableName().'.id'));

        return $this;
    }

    public function syncPreferenceTags(?array $names)
    {
        $names ??= [];

        $this->addTags($names);

        $this->tags()->where('type', TagType::Default)->where('visible_in_preferences', true)->whereNotIn('name', $names)->each(function ($tag) {
            event(new TagRemovedEvent($this, $tag));
        });

        $this->tags()->detach($this->tags()->where('type', TagType::Default)->where('visible_in_preferences', true)->whereNotIn('name', $names)->pluck(self::getTagTableName().'.id'));

        return $this->fresh('tags');
    }

    public function toExportRow(): array
    {
        return [
            'email' => $this->email,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'tags' => $this->tags->where('type', TagType::Default)->pluck('name')->implode(';'),
            'subscribed_at' => $this->subscribed_at?->format('Y-m-d H:i:s'),
            'unsubscribed_at' => $this->unsubscribed_at?->format('Y-m-d H:i:s'),
        ];
    }

    public function isUnconfirmed(): bool
    {
        return $this->status === SubscriptionStatus::Unconfirmed;
    }

    public function isSubscribed(): bool
    {
        return $this->status === SubscriptionStatus::Subscribed;
    }

    public function isUnsubscribed(): bool
    {
        return $this->status === SubscriptionStatus::Unsubscribed;
    }

    public function inAutomation(Automation $automation): bool
    {
        return $this->actions()->where('automation_id', $automation->id)->count() > 0;
    }

    protected static function newFactory(): SubscriberFactory
    {
        return new SubscriberFactory();
    }
}
