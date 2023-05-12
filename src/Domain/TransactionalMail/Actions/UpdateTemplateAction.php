<?php

namespace Spatie\Mailcoach\Domain\TransactionalMail\Actions;

use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailTemplate;
use Spatie\Mailcoach\Http\App\Requests\TransactionalMails\TransactionalMailTemplateRequest;

class UpdateTemplateAction
{
    use UsesMailcoachModels;

    public function execute(TransactionalMailTemplate $template, TransactionalMailTemplateRequest $request)
    {
        $template->update([
            'subject' => $request->subject,
            'body' => $request->html,
            'structured_html' => $request->structured_html,
            'to' => $request->to(),
            'cc' => $request->cc(),
            'bcc' => $request->bcc(),
        ]);

        return $template->refresh();
    }
}
