<?php

namespace Spatie\Mailcoach\Http\App\Controllers\EmailLists\Settings;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Http\App\Requests\EmailLists\Settings\UpdateEmailListOnboardingRequest;

class EmailListOnboardingController
{
    use AuthorizesRequests;

    public function edit(EmailList $emailList)
    {
        $this->authorize('update', $emailList);

        return view('mailcoach::app.emailLists.settings.onboarding', [
            'emailList' => $emailList->load(['tags', 'allowedFormSubscriptionTags']),
        ]);
    }

    public function update(EmailList $emailList, UpdateEmailListOnboardingRequest $request)
    {
        $this->authorize('update', $emailList);

        $emailList->update([
            'allow_form_subscriptions' => $request->allow_form_subscriptions ?? false,
            'allowed_form_extra_attributes' => $request->allowed_form_extra_attributes,
            'requires_confirmation' => $request->requires_confirmation ?? false,
            'redirect_after_subscribed' => $request->redirect_after_subscribed,
            'redirect_after_already_subscribed' => $request->redirect_after_already_subscribed,
            'redirect_after_subscription_pending' => $request->redirect_after_subscription_pending,
            'redirect_after_unsubscribed' => $request->redirect_after_unsubscribed,
            'send_welcome_mail' => $request->sendWelcomeMail(),
            'welcome_mail_subject' => $request->welcome_mail === UpdateEmailListOnboardingRequest::WELCOME_MAIL_CUSTOM_CONTENT
                ? $request->welcome_mail_subject
                : '',
            'welcome_mail_content' => $request->welcome_mail === UpdateEmailListOnboardingRequest::WELCOME_MAIL_CUSTOM_CONTENT
                ? $request->welcome_mail_content
                : '',
            'welcome_mail_delay_in_minutes' => $request->welcome_mail_delay_in_minutes ?? 0,
            'confirmation_mail_subject' => $request->sendDefaultConfirmationMail() ? null : $request->confirmation_mail_subject,
            'confirmation_mail_content' => $request->sendDefaultConfirmationMail() ? null : $request->confirmation_mail_content,
        ]);

        $emailList->allowedFormSubscriptionTags()->sync($request->allowedFormSubscriptionTags());

        flash()->success(__('List :emailList was updated', ['emailList' => $emailList->name]));

        return back();
    }
}
