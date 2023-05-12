<?php

namespace Spatie\Mailcoach\Domain\Audience\Actions\Subscribers;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Domain\Audience\Enums\SubscriberImportStatus;
use Spatie\Mailcoach\Domain\Audience\Mails\ImportSubscribersResultMail;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Audience\Models\SubscriberImport;
use Spatie\Mailcoach\Domain\Audience\Support\ImportSubscriberRow;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\SimpleExcel\SimpleExcelReader;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Spatie\TemporaryDirectory\TemporaryDirectory;

class ImportSubscribersAction
{
    use UsesMailcoachModels;

    protected ?User $user;

    protected string $dateTime;

    protected ?Media $importFile;

    protected SubscriberImport $subscriberImport;

    protected ?TemporaryDirectory $temporaryDirectory;

    protected ?SimpleExcelWriter $succeededImportsReport;

    protected ?SimpleExcelWriter $errorReport;

    public function execute(SubscriberImport $subscriberImport, ?User $user = null)
    {
        $this
            ->initialize($subscriberImport, $user)
            ->importSubscribers()
            ->logImportResults()
            ->storeReportMedia()
            ->unsubscribeMissing()
            ->cleanupTemporaryFiles()
            ->sendResultReport();
    }

    protected function initialize(SubscriberImport $subscriberImport, ?User $user): self
    {
        $this->subscriberImport = $subscriberImport;
        $this->user = $user;
        $this->dateTime = $subscriberImport->created_at->format('Y-m-d H:i:s');
        $this->importFile = $subscriberImport->getFirstMedia('importFile');
        $this->succeededImportsReport = $this->getSucceededImportsReport();
        $this->errorReport = $this->getErrorReport();

        return $this;
    }

    protected function importSubscribers(): self
    {
        try {
            $localImportFile = $this->storeLocalImportFile();

            SimpleExcelReader::create($localImportFile)
                ->getRows()
                ->map(fn (array $values) => new ImportSubscriberRow($this->subscriberImport->emailList, $values))
                ->filter(fn (ImportSubscriberRow $row) => $this->validateSubscriberEmail($row))
                ->filter(fn (ImportSubscriberRow $row) => $this->validateSubscriberStatus($row))
                ->each(fn (ImportSubscriberRow $row) => $this->importSubscriber($row));
        } catch (Exception $exception) {
            $this->errorReport->addRow([
                __(
                    "Couldn't finish importing subscribers. This error occurred: :error",
                    ['error' => $exception->getMessage()]
                ),
            ]);
        }

        return $this;
    }

    protected function importSubscriber(ImportSubscriberRow $row): Subscriber
    {
        $attributes = array_merge($row->getAttributes(), ['extra_attributes' => $row->getExtraAttributes()]);

        $subscriber = $this->getSubscriberClass()::createWithEmail($row->getEmail(), $attributes)
            ->skipConfirmation()
            ->doNotSendWelcomeMail()
            ->tags($row->tags())
            ->replaceTags($this->subscriberImport->replace_tags)
            ->subscribeTo($this->subscriberImport->emailList);

        $subscriber->update(['imported_via_import_uuid' => $this->subscriberImport->uuid]);

        $this->succeededImportsReport->addRow($row->getAllValues());

        return $subscriber;
    }

    protected function logImportResults(): self
    {
        $this->subscriberImport->update([
            'imported_subscribers_count' => $this->succeededImportsReport->getNumberOfRows(),
            'error_count' => $this->getErrorCount(),
            'status' => SubscriberImportStatus::COMPLETED,
        ]);

        return $this;
    }

    protected function storeReportMedia(): self
    {
        $this->subscriberImport
            ->addMedia($this->succeededImportsReport->getPath())
            ->toMediaCollection('importedUsersReport');

        $this->subscriberImport
            ->addMedia($this->errorReport->getPath())
            ->toMediaCollection('errorReport');

        return $this;
    }

    protected function unsubscribeMissing(): self
    {
        if ($this->getErrorCount() || ! $this->subscriberImport['unsubscribe_others']) {
            return $this;
        }

        $this->subscriberImport
            ->emailList
            ->subscribers()
            ->where(function (Builder $query) {
                $query
                    ->where('imported_via_import_uuid', '<>', $this->subscriberImport->uuid)
                    ->orWhereNull('imported_via_import_uuid');
            })
            ->cursor()
            ->each(fn (Subscriber $subscriber) => $subscriber->unsubscribe());

        return $this;
    }

    protected function cleanupTemporaryFiles(): self
    {
        $this->getTemporaryDirectory()->delete();

        return $this;
    }

    protected function sendResultReport(): self
    {
        if (! $this->user) {
            return $this;
        }

        Mail::mailer(config('mailcoach.mailer') ?? config('mail.default'))
            ->to($this->user->email)->send(new ImportSubscribersResultMail($this->subscriberImport));

        return $this;
    }

    protected function writeError(ImportSubscriberRow $row, string $reason)
    {
        $this->errorReport->addRow(array_merge([$reason], $row->getAllValues()));
    }

    /**
     * Store import file locally and return path to stored file.
     *
     * @return string
     */
    protected function storeLocalImportFile(): string
    {
        $localImportFile = $this->getTemporaryDirectory()
            ->path("import-file-{$this->dateTime}.{$this->importFile->extension}");

        file_put_contents($localImportFile, stream_get_contents($this->importFile->stream()));

        return $localImportFile;
    }

    protected function validateSubscriberEmail(ImportSubscriberRow $row): bool
    {
        if (! $row->hasValidEmail()) {
            $this->writeError($row, __('Does not have a valid email'));
        }

        return $row->hasValidEmail();
    }

    protected function validateSubscriberStatus(ImportSubscriberRow $row): bool
    {
        if ($this->subscriberImport->subscribe_unsubscribed) {
            return true;
        }

        $hasUnsubscribed = $row->hasUnsubscribed();

        if ($hasUnsubscribed) {
            $this->writeError($row, __('This email address was unsubscribed in the past.'));
        }

        return ! $hasUnsubscribed;
    }

    protected function getTemporaryDirectory(): TemporaryDirectory
    {
        return $this->temporaryDirectory
            ??= new TemporaryDirectory(storage_path('temp'));
    }

    protected function getSucceededImportsReport(): SimpleExcelWriter
    {
        return $this->succeededImportsReport
            ??= SimpleExcelWriter::create($this->getTemporaryDirectory()->path("imported-{$this->dateTime}.csv"))
            ->noHeaderRow();
    }

    protected function getErrorReport(): SimpleExcelWriter
    {
        return $this->errorReport
            ??= SimpleExcelWriter::create($this->getTemporaryDirectory()->path("errors-{$this->dateTime}.csv"))
            ->noHeaderRow()
            ->addRow(['Error', 'Values']);
    }

    protected function getErrorCount(): int
    {
        return $this->errorReport->getNumberOfRows() - 1;
    }
}
