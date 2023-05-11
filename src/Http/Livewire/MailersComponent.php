<?php

namespace Spatie\Mailcoach\Http\Livewire;

use Illuminate\Http\Request;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\App\Livewire\DataTableComponent;
use Spatie\Mailcoach\Http\App\Livewire\LivewireFlash;
use Spatie\Mailcoach\Http\App\Queries\MailersQuery;

class MailersComponent extends DataTableComponent
{
    use LivewireFlash;
    use UsesMailcoachModels;

    public function getTitle(): string
    {
        return __mc('Mailers');
    }

    public function getView(): string
    {
        return 'mailcoach::app.configuration.mailers.index';
    }

    public function getLayout(): string
    {
        return 'mailcoach::app.layouts.settings';
    }

    public function getLayoutData(): array
    {
        return [
            'title' => __mc('Mailers'),
        ];
    }

    public function markMailerDefault(int $id)
    {
        self::getMailerClass()::query()->update(['default' => false]);

        $mailer = self::getMailerClass()::find($id);

        if (! $mailer->ready_for_use) {
            $this->flashError(__mc('Mailer :mailer is not ready for use', ['mailer' => $mailer->name]));

            return;
        }

        $mailer->update(['default' => true]);

        $this->flash(__mc('Mailer :mailer marked as default', ['mailer' => $mailer->name]));
    }

    public function deleteMailer(int $id)
    {
        /** @var \Spatie\Mailcoach\Domain\Settings\Models\Mailer $mailer */
        $mailer = self::getMailerClass()::find($id);

        $configName = $mailer->configName();

        $mailer->delete();

        self::getEmailListClass()::each(function (EmailList $emailList) use ($configName) {
            if ($emailList->campaign_mailer === $configName) {
                $emailList->campaign_mailer = null;
            }

            if ($emailList->automation_mailer === $configName) {
                $emailList->automation_mailer = null;
            }

            if ($emailList->transactional_mailer === $configName) {
                $emailList->transactional_mailer = null;
            }

            $emailList->save();
        });

        $this->flash(__mc('Mailer :mailer successfully deleted', ['mailer' => $mailer->name]));
    }

    public function getData(Request $request): array
    {
        return [
            'mailers' => (new MailersQuery($request))->paginate(),
            'totalMailersCount' => self::getMailerClass()::count(),
        ];
    }
}
