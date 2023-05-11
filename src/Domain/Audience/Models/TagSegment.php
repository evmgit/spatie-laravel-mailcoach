<?php

namespace Spatie\Mailcoach\Domain\Audience\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Spatie\Mailcoach\Database\Factories\TagSegmentFactory;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Models\HasUuid;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class TagSegment extends Model
{
    use HasUuid;
    use HasFactory;
    use UsesMailcoachModels;

    public $table = 'mailcoach_segments';

    public $casts = [
        'all_positive_tags_required' => 'boolean',
        'all_negative_tags_required' => 'boolean',
    ];

    public $guarded = [];

    public function campaigns(): HasMany
    {
        return $this->hasMany(self::getCampaignClass());
    }

    public function emailList(): BelongsTo
    {
        return $this->belongsTo(self::getEmailListClass(), 'email_list_id');
    }

    public function positiveTags(): BelongsToMany
    {
        return $this
            ->belongsToMany(self::getTagClass(), 'mailcoach_positive_segment_tags', 'segment_id', 'tag_id')
            ->orderBy('name');
    }

    public function negativeTags(): BelongsToMany
    {
        return $this
            ->belongsToMany(self::getTagClass(), 'mailcoach_negative_segment_tags', 'segment_id', 'tag_id')
            ->orderBy('name');
    }

    public function syncPositiveTags(array $tagNames): self
    {
        return $this->syncTags($tagNames, $this->positiveTags());
    }

    public function syncNegativeTags(array $tagNames): self
    {
        return $this->syncTags($tagNames, $this->negativeTags());
    }

    protected function syncTags(array $tagNames, BelongsToMany $tagsRelation)
    {
        $tags = self::getTagClass()::query()->whereIn('name', $tagNames)->where('email_list_id', $this->email_list_id)->get();

        $tagsRelation->sync($tags);

        return $this->refresh();
    }

    public function getSubscribersQuery(): Builder
    {
        $query = $this->emailList->subscribers()->getQuery();

        $this->scopeOnTags($query);

        return $query;
    }

    public function getSubscribersCount(): int
    {
        return once(function () {
            return $this->getSubscribersQuery()->count();
        });
    }

    public function scopeOnTags(Builder $subscribersQuery): void
    {
        $this->buildPositiveTagsQuery($subscribersQuery);

        $this->buildNegativeTagsQuery($subscribersQuery);
    }

    public function description(Campaign $campaign): string
    {
        return $this->name;
    }

    protected function buildPositiveTagsQuery(Builder $subscribersQuery): void
    {
        if (! $this->positiveTags()->count()) {
            return;
        }

        if ($this->all_positive_tags_required) {
            $subscribersQuery
                ->where(
                    DB::table('mailcoach_email_list_subscriber_tags')
                        ->selectRaw('count(*)')
                        ->where('mailcoach_subscribers.id', DB::raw('mailcoach_email_list_subscriber_tags.subscriber_id'))
                        ->whereIn('mailcoach_email_list_subscriber_tags.tag_id', $this->positiveTags()->pluck('mailcoach_tags.id')->toArray()),
                    '>=', $this->positiveTags()->count()
                );

            return;
        }

        $subscribersQuery->addWhereExistsQuery(DB::table('mailcoach_email_list_subscriber_tags')
            ->where('mailcoach_subscribers.id', DB::raw('mailcoach_email_list_subscriber_tags.subscriber_id'))
            ->whereIn('mailcoach_email_list_subscriber_tags.tag_id', $this->positiveTags()->pluck('mailcoach_tags.id')->toArray())
        );
    }

    protected function buildNegativeTagsQuery(Builder $subscribersQuery): void
    {
        if (! $this->negativeTags()->count()) {
            return;
        }

        if ($this->all_negative_tags_required) {
            $subscribersQuery
                ->where(
                    DB::table('mailcoach_email_list_subscriber_tags')
                        ->selectRaw('count(*)')
                        ->where(self::getSubscriberTableName().'.id', DB::raw('mailcoach_email_list_subscriber_tags.subscriber_id'))
                        ->whereIn('mailcoach_email_list_subscriber_tags.tag_id', $this->negativeTags()->pluck(self::getTagTableName().'.id')->toArray()),
                    '<', $this->negativeTags()->count()
                );

            return;
        }

        $subscribersQuery->addWhereExistsQuery(DB::table('mailcoach_email_list_subscriber_tags')
            ->where(self::getSubscriberTableName().'.id', DB::raw('mailcoach_email_list_subscriber_tags.subscriber_id'))
            ->whereIn('mailcoach_email_list_subscriber_tags.tag_id', $this->negativeTags()->pluck(self::getTagTableName().'.id')->toArray()),
            not: true
        );
    }

    protected static function newFactory(): TagSegmentFactory
    {
        return new TagSegmentFactory();
    }
}
