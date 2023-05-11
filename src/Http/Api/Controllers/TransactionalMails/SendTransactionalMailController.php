<?php

namespace Spatie\Mailcoach\Http\Api\Controllers\TransactionalMails;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Domain\TransactionalMail\Mails\TransactionalMail;
use Spatie\Mailcoach\Domain\TransactionalMail\Support\AddressNormalizer;
use Spatie\Mailcoach\Http\Api\Controllers\Concerns\RespondsToApiRequests;
use Spatie\Mailcoach\Http\Api\Requests\SendTransactionalMailRequest;
use Spatie\Mailcoach\Mailcoach;

class SendTransactionalMailController
{
    use RespondsToApiRequests;
    use AuthorizesRequests;
    use UsesMailcoachModels;

    public function __invoke(SendTransactionalMailRequest $request)
    {
        $this->authorize('send', [static::getSendClass(), $request->getFromEmail()]);

        $normalizer = new AddressNormalizer();

        $mail = new TransactionalMail(
            mailName: $request->get('mail_name'),
            subject: $request->get('subject'),
            from: $normalizer->normalize($request->get('from')),
            to: $normalizer->normalize($request->get('to')),
            cc: $normalizer->normalize($request->get('cc')),
            bcc: $normalizer->normalize($request->get('bcc')),
            replyTo: $normalizer->normalize($request->get('reply_to')),
            mailer: $request->get('mailer'),
            replacements: $request->replacements(),
            attachments: $request->attachments(),
            store: $request->shouldStoreMail(),
            html: $request->html,
        );

        Mail::mailer($request->get('mailer', Mailcoach::defaultTransactionalMailer()))->send($mail);

        return $this->respondOk();
    }
}
