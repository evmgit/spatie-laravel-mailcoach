<?php

namespace Spatie\Mailcoach\Domain\Audience\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Mailcoach\Database\Factories\TagSegmentFactory;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;

class TagSegment extends Model
{
    use HasFactory;

    public $table = 'mailcoach_segments';

    public $casts = [
        'all_positive_tags_required' => 'boolean',
        'all_negative_tags_required' => 'boolean',
    ];

    public $guarded = [];

    public function campaigns(): HasMany
    {
        return $this->hasMany(config('mailcoach.models.campaign'));
    }

    public function emailList(): BelongsTo
    {
        return $this->belongsTo(config('mailcoach.models.email_list'), 'email_list_id');
    }

    public function positiveTags(): BelongsToMany
    {
        return $this
            ->belongsToMany(Tag::class, 'mailcoach_positive_segment_tags', 'segment_id', 'tag_id')
            ->orderBy('name');
    }

    public function negativeTags(): BelongsToMany
    {
        return $this
            ->belongsToMany(Tag::class, 'mailcoach_negative_segment_tags', 'segment_id', 'tag_id')
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
        $tags = Tag::query()->whereIn('name', $tagNames)->where('email_list_id', $this->email_list_id)->get();

        $tagsRelation->sync($tags);

        return $this->refresh();
    }

    public function getSubscribersQuery(): Builder
    {
        $query = $this->emailList->subscribers()->getQuery();

        $this->scopeOnTags($query);

        return $query;
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
                ->whereHas('tags', function (Builder $query) {
                    $query->whereIn('mailcoach_tags.id', $this->positiveTags()->pluck('tag_id'));
                }, '>=', $this->positiveTags()->count());

            return;
        }

        $subscribersQuery->whereHas('tags', function (Builder $query) {
            $query->whereIn('mailcoach_tags.id', $this->positiveTags()->pluck('mailcoach_tags.id')->toArray());
        });
    }

    protected function buildNegativeTagsQuery(Builder $subscribersQuery): void
    {
        if (! $this->negativeTags()->count()) {
            return;
        }

        if ($this->all_negative_tags_required) {
            $subscribersQuery
                ->whereHas('tags', function (Builder $query) {
                    $query->whereIn('mailcoach_tags.id', $this->negativeTags()->pluck('tag_id'));
                }, '<', $this->negativeTags()->count());

            return;
        }

        $subscribersQuery->whereDoesntHave('tags', function (Builder $query) {
            $query->whereIn('mailcoach_tags.id', $this->negativeTags()->pluck('mailcoach_tags.id')->toArray());
        });
    }

    protected static function newFactory(): TagSegmentFactory
    {
        return new TagSegmentFactory();
    }
}
