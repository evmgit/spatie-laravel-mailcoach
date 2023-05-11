<?php

namespace Spatie\Mailcoach\Domain\Shared\Jobs\Export;

use Illuminate\Support\Facades\DB;

class ExportEmailListsJob extends ExportJob
{
    /**
     * @param  string  $path
     * @param  array<int>  $selectedEmailLists
     */
    public function __construct(protected string $path, protected array $selectedEmailLists)
    {
    }

    public function name(): string
    {
        return 'Email Lists';
    }

    public function execute(): void
    {
        $emailLists = DB::table(self::getEmailListTableName())
            ->whereIn('id', $this->selectedEmailLists)
            ->get();

        $allowedSubscriptionTags = DB::table('mailcoach_email_list_allow_form_subscription_tags')
            ->select(
                'mailcoach_email_list_allow_form_subscription_tags.*',
                DB::raw(self::getEmailListTableName().'.uuid as email_list_uuid')
            )
            ->join(self::getEmailListTableName(), self::getEmailListTableName().'.id', '=', 'mailcoach_email_list_allow_form_subscription_tags.email_list_id')
            ->whereIn('email_list_id', $this->selectedEmailLists)
            ->get();

        $this->writeFile('email_lists.csv', $emailLists);
        $this->writeFile('email_list_allow_form_subscription_tags.csv', $allowedSubscriptionTags);
        $this->addMeta('email_lists_count', $emailLists->count());
        $this->addMeta('email_list_allow_form_subscription_tags_count', $allowedSubscriptionTags->count());
    }
}
