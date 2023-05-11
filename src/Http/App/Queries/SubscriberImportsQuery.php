<?php

namespace Spatie\Mailcoach\Http\App\Queries;

use Illuminate\Http\Request;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class SubscriberImportsQuery extends QueryBuilder
{
    use UsesMailcoachModels;

    public function __construct(EmailList $emailList, Request $request)
    {
        parent::__construct(
            self::getSubscriberImportClass()::query()
                ->where('email_list_id', $emailList->id)
                ->with('emailList'),
            $request,
        );

        $this
            ->defaultSort('-created_at')
            ->allowedSorts(['created_at', 'status', 'imported_subscribers_count'])
            ->allowedFilters(
                AllowedFilter::exact('uuid'),
            );
    }
}
