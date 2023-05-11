<?php

namespace Spatie\Mailcoach\Domain\Shared\Jobs\Export;

use Illuminate\Support\Facades\DB;

class ExportTransactionalMailTemplatesJob extends ExportJob
{
    /**
     * @param  string  $path
     * @param  array<int>  $selectedTransactionalMailTemplates
     */
    public function __construct(protected string $path, protected array $selectedTransactionalMailTemplates)
    {
    }

    public function name(): string
    {
        return 'Transactional Mail Templates';
    }

    public function execute(): void
    {
        $templates = DB::table(self::getTransactionalMailTableName())
            ->whereIn('id', $this->selectedTransactionalMailTemplates)
            ->get();

        $this->writeFile('transactional_mail_templates.csv', $templates);
        $this->addMeta('transactional_mail_templates_count', $templates->count());
    }
}
