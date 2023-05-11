<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Audience;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;
use Spatie\Mailcoach\Domain\Audience\Actions\Subscribers\CreateSimpleExcelReaderAction;
use Spatie\Mailcoach\Domain\Audience\Enums\SubscriberImportStatus;
use Spatie\Mailcoach\Domain\Audience\Jobs\ImportSubscribersJob;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\App\Livewire\DataTableComponent;
use Spatie\Mailcoach\Http\App\Queries\SubscriberImportsQuery;
use Spatie\Mailcoach\MainNavigation;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Spatie\TemporaryDirectory\TemporaryDirectory;

class SubscriberImportsComponent extends DataTableComponent
{
    use WithFileUploads;
    use AuthorizesRequests;
    use UsesMailcoachModels;

    public string $sort = '-created_at';

    public EmailList $emailList;

    public string $replaceTags = 'append';

    public bool $subscribeUnsubscribed = false;

    public bool $unsubscribeMissing = false;

    public bool $showForm = true;

    public $file;

    public function mount(EmailList $emailList)
    {
        $this->emailList = $emailList;

        app(MainNavigation::class)->activeSection()?->add($this->emailList->name, route('mailcoach.emailLists'));

        $this->showForm = self::getSubscriberImportClass()::query()
            ->where('email_list_id', $this->emailList->id)
            ->count() === 0;
    }

    public function upload()
    {
        info($this->file->guessExtension());

        $this->validate([
            'file' => ['file', 'mimes:txt,csv,xls,xlsx'],
        ]);

        $this->authorize('update', $this->emailList);

        /** @var \Livewire\TemporaryUploadedFile $file */
        $file = $this->file;
        $path = $file->store('subscriber-import');

        /** @var \Spatie\Mailcoach\Domain\Audience\Models\SubscriberImport $subscriberImport */
        $subscriberImport = self::getSubscriberImportClass()::create([
            'email_list_id' => $this->emailList->id,
            'subscribe_unsubscribed' => $this->subscribeUnsubscribed,
            'unsubscribe_others' => $this->unsubscribeMissing,
            'replace_tags' => $this->replaceTags === 'replace',
        ]);

        $subscriberImport->addMediaFromDisk($path, $file->disk)->toMediaCollection('importFile');

        $reader = app(CreateSimpleExcelReaderAction::class)->execute($subscriberImport);

        if (! in_array('email', $reader->getHeaders() ?? []) && ! in_array('Email Address', $reader->getHeaders() ?? [])) {
            $subscriberImport->delete();
            Storage::disk($file->disk)->delete($path);
            $this->addError('file', __mc('No header row found. Make sure your first row has at least 1 column with "email"'));

            return;
        }

        $user = auth()->user();

        dispatch(new ImportSubscribersJob($subscriberImport, $user instanceof User ? $user : null));

        $this->flash(__mc('Your file has been uploaded. Follow the import status in the list below.'));

        $this->file = null;
        $this->showForm = false;
    }

    public function downloadAttatchment(int $subscriberImport, string $collection)
    {
        $subscriberImport = self::getSubscriberImportClass()::find($subscriberImport);

        if ($collection === 'errorReport') {
            $temporaryDirectory = TemporaryDirectory::make();

            app()->terminating(function () use ($temporaryDirectory) {
                $temporaryDirectory->delete();
            });

            return response()->download(
                SimpleExcelWriter::create($temporaryDirectory->path('errorReport.csv'), 'csv')
                    ->noHeaderRow()
                    ->addRows($subscriberImport->errors ?? [])
                    ->getPath()
            );
        }

        abort_unless((bool) $subscriberImport->getMediaCollection($collection), 403);

        $subscriberImport = self::getSubscriberImportClass()::find($subscriberImport->id);

        return $subscriberImport->getFirstMedia($collection);
    }

    public function downloadExample()
    {
        $temporaryDirectory = TemporaryDirectory::make();

        app()->terminating(function () use ($temporaryDirectory) {
            $temporaryDirectory->delete();
        });

        return response()->download(
            SimpleExcelWriter::create($temporaryDirectory->path('subscribers-example.csv'))
                ->addRow(['email' => 'john@doe.com', 'first_name' => 'John', 'last_name' => 'Doe', 'tags' => 'one;two'])
                ->getPath()
        );
    }

    public function deleteImport(int $id)
    {
        $import = self::getSubscriberImportClass()::find($id);

        $this->authorize('delete', $import);

        $import->delete();

        $this->flash(__mc('Import was deleted.'));
    }

    public function restartImport(int $id)
    {
        $import = self::getSubscriberImportClass()::find($id);
        $import->update(['status' => SubscriberImportStatus::Pending]);

        dispatch(new ImportSubscribersJob($import, Auth::user()));

        $this->flash(__mc('Import successfully restarted.'));
    }

    public function getTitle(): string
    {
        return __mc('Import subscribers');
    }

    public function getView(): string
    {
        return 'mailcoach::app.emailLists.subscribers.import';
    }

    public function getLayout(): string
    {
        return 'mailcoach::app.emailLists.layouts.emailList';
    }

    public function getLayoutData(): array
    {
        return [
            'emailList' => $this->emailList,
        ];
    }

    public function getData(Request $request): array
    {
        $this->authorize('view', $this->emailList);

        $subscriberImportsQuery = new SubscriberImportsQuery($this->emailList, $request);

        return [
            'subscriberImports' => $subscriberImportsQuery->paginate($request->per_page),
            'allSubscriberImportsCount' => self::getSubscriberImportClass()::query()
                ->where('email_list_id', $this->emailList->id)
                ->count(),
            'emailList' => $this->emailList,
        ];
    }
}
