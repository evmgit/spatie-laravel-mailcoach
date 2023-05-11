<?php

namespace Spatie\Mailcoach\Http\App\Queries;

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Http\Request;
use Spatie\Mailcoach\Domain\Audience\Enums\SubscriptionStatus;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\App\Queries\Filters\SearchFilter;
use Spatie\Mailcoach\Http\App\Queries\Filters\SubscriberStatusFilter;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class EmailListSubscribersQuery extends QueryBuilder
{
    use UsesMailcoachModels;

    public function __construct(EmailList $emailList, ?Request $request = null)
    {
        $subscribersQuery = self::getSubscriberClass()::query()
            ->where(self::getSubscriberTableName().'.email_list_id', $emailList->id)
            ->distinct(self::getSubscriberTableName().'.id')
            ->with('emailList', 'tags');

        parent::__construct($subscribersQuery, $request);

        $this
            ->allowedSorts('created_at', 'updated_at', 'subscribed_at', 'unsubscribed_at', 'email', 'first_name', 'last_name', 'id')
            ->allowedFilters(
                AllowedFilter::callback('email', function (Builder $query, $value) {
                    $value = trim($value);

                    if (config('mailcoach.encryption.enabled')) {
                        return $query->where(function (Builder $query) use ($value) {
                            $query->whereBlind('email', 'email_first_part', $value)
                                ->orWhereBlind('email', 'email_second_part', $value);
                        });
                    }

                    return $query->where('email', $value);
                }),
                AllowedFilter::custom('search', new SearchFilter()),
                AllowedFilter::custom('status', new SubscriberStatusFilter())
            );

        $request?->input('filter.status') === SubscriptionStatus::Unsubscribed
            ? $this->defaultSort('-unsubscribed_at')
            : $this->defaultSort('-created_at', '-id');
    }
}
