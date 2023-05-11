<?php

namespace Spatie\Mailcoach\Http\Livewire;

use Illuminate\Http\Request;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\App\Livewire\DataTableComponent;
use Spatie\Mailcoach\Http\App\Livewire\LivewireFlash;
use Spatie\Mailcoach\Http\App\Queries\WebhooksQuery;

class WebhooksComponent extends DataTableComponent
{
    use LivewireFlash;
    use UsesMailcoachModels;

    public function getTitle(): string
    {
        return __mc('Webhooks');
    }

    public function getView(): string
    {
        return 'mailcoach::app.configuration.webhooks.index';
    }

    public function getLayout(): string
    {
        return 'mailcoach::app.layouts.settings';
    }

    public function getLayoutData(): array
    {
        return [
            'title' => __mc('Webhooks'),
        ];
    }

    public function deleteWebhook(int $id)
    {
        $webhook = self::getWebhookConfigurationClass()::find($id);

        $webhook->delete();

        $this->flash(__mc('Webhook :webhook successfully deleted', ['webhook' => $webhook->name]));
    }

    public function getData(Request $request): array
    {
        return [
            'webhooks' => (new WebhooksQuery($request))->paginate(),
            'totalWebhooksCount' => self::getWebhookConfigurationClass()::count(),
        ];
    }
}
