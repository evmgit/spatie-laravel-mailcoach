<?php

namespace Spatie\Mailcoach\Domain\Shared\Support\Editor;

use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Campaign\Models\Concerns\HasHtmlContent;
use Spatie\Mailcoach\Domain\Campaign\Models\Template;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailTemplate;

class TextEditor implements Editor
{
    public function render(HasHtmlContent $model): string
    {
        $campaignClass = config('mailcoach.models.campaign', Campaign::class);
        $automationMailClass = config('mailcoach.models.automation_mail', AutomationMail::class);
        $templateClass = config('mailcoach.models.template', Template::class);
        $transactionalMailTemplateClass = config('mailcoach.models.transactional_mail_template', TransactionalMailTemplate::class);
        
        return match ($model::class) {
            $campaignClass => $this->renderForCampaign($model),
            $templateClass => $this->renderForCampaignTemplate($model),
            $automationMailClass => $this->renderForAutomationMail($model),
            $transactionalMailTemplateClass => $this->renderForTransactionalMailTemplate($model),
        };
    }

    protected function renderForCampaign(HasHtmlContent $model): string
    {
        return (string)view('mailcoach::app.campaigns.partials.textEditor', [
            'html' => $model->getHtml(),
            'campaign' => $model,
        ])->render();
    }

    protected function renderForCampaignTemplate(HasHtmlContent $model): string
    {
        return (string)view('mailcoach::app.campaigns.templates.partials.textEditor', [
            'html' => $model->getHtml(),
            'template' => $model,
        ])->render();
    }

    protected function renderForAutomationMail(HasHtmlContent $model): string
    {
        return (string)view('mailcoach::app.automations.mails.partials.textEditor', [
            'html' => $model->getHtml(),
            'mail' => $model,
        ])->render();
    }

    protected function renderForTransactionalMailTemplate(HasHtmlContent $model): string
    {
        return (string)view('mailcoach::app.transactionalMails.templates.partials.textEditor', [
            'html' => $model->getHtml(),
            'template' => $model,
        ])->render();
    }
}
