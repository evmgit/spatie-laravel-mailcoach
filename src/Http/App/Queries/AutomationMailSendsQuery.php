<?php

namespace Spatie\Mailcoach\Http\App\Queries;

use Illuminate\Http\Request;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\App\Queries\Filters\FuzzyFilter;
use Spatie\Mailcoach\Http\App\Queries\Filters\SendTypeFilter;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class AutomationMailSendsQuery extends QueryBuilder
{
    use UsesMailcoachModels;

    public function __construct(AutomationMail $automationMail, ?Request $request = null)
    {
        parent::__construct(self::getSendClass()::query(), $request);

        $this
            ->addSelect(['subscriber_email' => self::getSubscriberClass()::select('email')
                ->whereColumn('subscriber_id', "{$this->getSubscriberTableName()}.id")
                ->limit(1),
            ])
            ->with('feedback')
            ->where('automation_mail_id', $automationMail->id)
            ->defaultSort('created_at')
            ->with(['subscriber'])
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
