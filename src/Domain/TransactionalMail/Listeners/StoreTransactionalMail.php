<?php

namespace Spatie\Mailcoach\Domain\TransactionalMail\Listeners;

use Illuminate\Mail\Events\MessageSending;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Domain\TransactionalMail\Events\TransactionalMailStored;
use Spatie\Mailcoach\Domain\TransactionalMail\Support\TransactionalMailMessageConfig;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Part\DataPart;

class StoreTransactionalMail
{
    use UsesMailcoachModels;

    public function handle(MessageSending $sending): void
    {
        $message = $sending->message;

        $messageConfig = TransactionalMailMessageConfig::createForMessage($message);

        if (! $messageConfig->shouldStore()) {
            return;
        }

        $transactionalMail = static::getTransactionalMailLogItemClass()::create([
            'subject' => $message->getSubject(),
            'from' => $this->convertToNamedArray($message->getFrom()),
            'to' => $this->convertToNamedArray($message->getTo()),
            'cc' => $this->convertToNamedArray($message->getCc()),
            'bcc' => $this->convertToNamedArray($message->getBcc()),
            'body' => $message->getHtmlBody() ?? $message->getTextBody(),
            'mailable_class' => $messageConfig->getMailableClass(),
            'attachments' => collect($message->getAttachments())->map(fn (DataPart $dataPart) => $dataPart->getFilename()),
        ]);

        $send = self::getSendClass()::create([
            'transactional_mail_log_item_id' => $transactionalMail->id,
            'sent_at' => now(),
        ]);

        $messageId = $sending->message->generateMessageId();
        $send->storeTransportMessageId($messageId);

        $sending->message->getHeaders()->addIdHeader('Message-ID', $messageId);
        $sending->message->getHeaders()->addTextHeader('mailcoach-send-uuid', $send->uuid);

        // Add Sendgrid header
        $sending->message->getHeaders()->addTextHeader(
            'X-SMTPAPI',
            json_encode(['unique_args' => ['send_uuid' => $send->uuid]])
        );

        event(new TransactionalMailStored($transactionalMail, $sending));
    }

    public function convertToNamedArray(?array $persons): array
    {
        return collect($persons ?? [])
            ->map(fn (Address $address) => [
                'email' => $address->getAddress(), 'name' => $address->getName(),
            ])
            ->values()
            ->toArray();
    }
}
