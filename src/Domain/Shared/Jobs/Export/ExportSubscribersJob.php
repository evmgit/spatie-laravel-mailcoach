<?php

namespace Spatie\Mailcoach\Domain\Shared\Jobs\Export;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class ExportSubscribersJob extends ExportJob
{
    use UsesMailcoachModels;

    /**
     * @param  string  $path
     * @param  array<int>  $selectedEmailLists
     */
    public function __construct(protected string $path, protected array $selectedEmailLists)
    {
    }

    public function name(): string
    {
        return 'Subscribers';
    }

    public function execute(): void
    {
        $subscribersCount = 0;

        DB::table(self::getSubscriberTableName())
            ->select(self::getSubscriberTableName().'.*', DB::raw(self::getEmailListTableName().'.uuid as email_list_uuid'))
            ->join(self::getEmailListTableName(), self::getEmailListTableName().'.id', self::getSubscriberTableName().'.email_list_id')
            ->orderBy('id')
            ->whereIn('email_list_id', $this->selectedEmailLists)
            ->chunk(10_000, function (Collection $subscribers, $index) use (&$subscribersCount) {
                $subscribersCount += $subscribers->count();

                if (config('mailcoach.encryption.enabled')) {
                    $subscribers = $subscribers->map(function ($subscriber) {
                        $class = self::getSubscriberClass();
                        $subscriberModel = (new $class((array) $subscriber));
                        $subscriberModel->decryptRow();

                        $subscriber->email = $subscriberModel->email;
                        $subscriber->first_name = $subscriberModel->first_name;
                        $subscriber->last_name = $subscriberModel->last_name;

                        return $subscriber;
                    });
                }

                $this->writeFile("subscribers-{$index}.csv", $subscribers);
            });

        $this->addMeta('subscribers_count', $subscribersCount);
    }

    private function parseKey(string $key): string
    {
        $key = trim($key);

        if (Str::startsWith($key, $prefix = 'base64:')) {
            $key = base64_decode(Str::after($key, $prefix));
        }

        return $key;
    }
}
