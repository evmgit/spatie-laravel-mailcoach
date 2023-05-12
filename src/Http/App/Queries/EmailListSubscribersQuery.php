<?php

namespace Spatie\Mailcoach\Http\App\Queries;

use Spatie\Mailcoach\Domain\Audience\Enums\SubscriptionStatus;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\App\Queries\Filters\FuzzyFilter;
use Spatie\Mailcoach\Http\App\Queries\Filters\SubscriberStatusFilter;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class EmailListSubscribersQuery extends QueryBuilder
{
    use UsesMailcoachModels;

    public function __construct(EmailList $emailList)
    {
        $subscribersQuery = $this->getSubscriberClass()::query()
            ->where('email_list_id', $emailList->id)
            ->with('emailList', 'tags');

        parent::__construct($subscribersQuery);

        $this
            ->allowedSorts('created_at', 'unsubscribed_at', 'email', 'first_name', 'last_name', 'id')
            ->allowedFilters(
                'email',
                AllowedFilter::custom('search', new FuzzyFilter(
                    'email',
                    'first_name',
                    'last_name',
                    'tags.name'
                )),
                AllowedFilter::custom('status', new SubscriberStatusFilter())
            );

        request()->input('filter.status') === SubscriptionStatus::UNSUBSCRIBED
            ? $this->defaultSort('-unsubscribed_at')
            : $this->defaultSort('-created_at', '-id');
    }
}
