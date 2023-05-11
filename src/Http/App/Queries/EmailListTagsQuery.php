<?php

namespace Spatie\Mailcoach\Http\App\Queries;

use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Tag;
use Spatie\Mailcoach\Http\App\Queries\Filters\FuzzyFilter;
use Spatie\Mailcoach\Http\App\Queries\Filters\TagTypeFilter;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class EmailListTagsQuery extends QueryBuilder
{
    public function __construct(EmailList $emailList, ?Request $request)
    {
        $query = Tag::query()
            ->addSelect(['subscriber_count' => function (Builder $query) {
                $query
                    ->selectRaw('count(id)')
                    ->from('mailcoach_email_list_subscriber_tags')
                    ->whereColumn('mailcoach_email_list_subscriber_tags.tag_id', 'mailcoach_tags.id');
            }]);

        parent::__construct($query, $request);

        $this

            ->where('email_list_id', $emailList->id)
            ->defaultSort('name')
            ->allowedSorts('name', 'updated_at', 'subscriber_count', 'visible_in_preferences')
            ->allowedFilters(
                AllowedFilter::custom('search', new FuzzyFilter(
                    'name'
                )),
                AllowedFilter::custom('type', new TagTypeFilter()),
            );
    }
}
