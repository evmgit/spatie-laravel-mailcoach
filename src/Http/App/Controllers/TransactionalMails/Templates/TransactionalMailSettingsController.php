<?php

namespace Spatie\Mailcoach\Http\App\Controllers\TransactionalMails\Templates;

use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailTemplate;
use Spatie\Mailcoach\Http\App\Requests\TransactionalMails\UpdateTransactionalMailSettingsRequest;

class TransactionalMailSettingsController
{
    public function edit(TransactionalMailTemplate $template)
    {
        return view('mailcoach::app.transactionalMails.templates.settings', compact('template'));
    }

    public function update(UpdateTransactionalMailSettingsRequest $request, TransactionalMailTemplate $template)
    {
        $template->update([
            'name' => $request->name,
            'type' => $request->type,
            'store_mail' => $request->boolean('store_mail'),
            'track_opens' => $request->boolean('track_opens'),
            'track_clicks' => $request->boolean('track_clicks'),
        ]);

        flash()->success('The settings have been updated');

        return back();
    }
}
