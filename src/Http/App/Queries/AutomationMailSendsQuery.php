<?php

namespace Spatie\Mailcoach\Http\App\Queries;

use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\App\Queries\Filters\FuzzyFilter;
use Spatie\Mailcoach\Http\App\Queries\Filters\SendTypeFilter;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class AutomationMailSendsQuery extends QueryBuilder
{
    use UsesMailcoachModels;

    public function __construct(AutomationMail $automationMail)
    {
        parent::__construct($this->getSendClass()::query());

        $this
            ->addSelect(['subscriber_email' => $this->getSubscriberClass()::select('email')
                ->whereColumn('subscriber_id', "{$this->getSubscriberTableName()}.id")
                ->limit(1),
            ])
            ->with('feedback')
            ->where('automation_mail_id', $automationMail->id)
            ->defaultSort('created_at')
            ->with(['campaign', 'subscriber'])
            ->allowedSorts(
                'sent_at',
                'subscriber_email',
            )
            ->allowedFilters(
                AllowedFilter::custom('type', new SendTypeFilter()),
                AllowedFilter::custom('search', new FuzzyFilter('subscriber.email'))
            );
    }
}
