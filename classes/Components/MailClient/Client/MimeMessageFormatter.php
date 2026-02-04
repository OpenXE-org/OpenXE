<?php

declare(strict_types=1);

namespace Xentral\Components\MailClient\Client;

use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Part\DataPart;
use Xentral\Components\MailClient\Exception\InvalidArgumentException;
use Xentral\Components\MailClient\Exception\MessageFormatException;
use Xentral\Components\Mailer\Data\EmailAttachmentInterface;
use Xentral\Components\Mailer\Data\EmailMessage;
use Xentral\Components\Mailer\Data\EmailRecipient;
use Xentral\Components\Mailer\Data\FileAttachment;
use Xentral\Components\Mailer\Data\ImageAttachment;
use Xentral\Components\Mailer\Data\StringAttachment;

final class MimeMessageFormatter implements MimeMessageFormatterInterface
{
    /**
     * @param EmailMessage   $email
     * @param EmailRecipient $from
     * @param string|null    $messageId
     *
     * @throws MessageFormatException
     *
     * @return string
     */
    public function formatMessage(EmailMessage $email, EmailRecipient $from, ?string $messageId = null): string
    {
        if ($messageId !== null && !preg_match('/^<.*@.*>$/', $messageId)) {
            throw new InvalidArgumentException('message id must be RFC 5322 conform');
        }
        $message = new Email();
        $message->from($this->createAddress($from));
        $message->subject($email->getSubject());
        $message->replyTo($this->createAddress($from));

        foreach ($email->getRecipients() as $recipient) {
            $message->addTo($this->createAddress($recipient));
        }
        foreach ($email->getCcRecipients() as $recipient) {
            $message->addCc($this->createAddress($recipient));
        }
        foreach ($email->getBccRecipients() as $recipient) {
            $message->addBcc($this->createAddress($recipient));
        }

        if ($messageId !== null) {
            $message->getHeaders()->addIdHeader('Message-ID', trim($messageId, '<>'));
        }

        if ($email->isHtml()) {
            $message->html($email->getBody(), 'iso-8859-1');
            $message->text($this->convertHtmlToPlainText($email->getBody()), 'iso-8859-1');
        } else {
            $message->text($email->getBody(), 'iso-8859-1');
        }

        foreach ($email->getAttachments() as $attachment) {
            $message->addPart($this->createAttachmentPart($attachment));
        }

        return $message->toString();
    }

    /**
     * @param EmailAttachmentInterface $attachment
     *
     * @throws MessageFormatException
     *
     * @return DataPart
     */
    private function createAttachmentPart(EmailAttachmentInterface $attachment): DataPart
    {
        switch (get_class($attachment)) {
            case FileAttachment::class:
                $part = DataPart::fromPath($attachment->getPath(), $attachment->getName(), $attachment->getType());
                break;
            case StringAttachment::class:
                $part = new DataPart(
                    $attachment->getContent(),
                    $attachment->getName(),
                    $attachment->getType(),
                    $attachment->getEncoding()
                );
                break;
            case ImageAttachment::class:
                $part = DataPart::fromPath($attachment->getPath(), $attachment->getName(), $attachment->getType());
                break;
            default:
                throw new MessageFormatException(
                    sprintf('unrecognized attachment class "%s"', get_class($attachment))
                );
        }
        if (strtolower($attachment->getDisposition()) === EmailAttachmentInterface::DISPOSITION_INLINE) {
            $part->asInline();
        }

        return $part;
    }

    /**
     * @param EmailRecipient $recipient
     *
     * @return Address
     */
    private function createAddress(EmailRecipient $recipient): Address
    {
        return new Address($recipient->getEmail(), $recipient->getName());
    }

    /**
     * @param string $html
     *
     * @return string
     */
    private function convertHtmlToPlainText(string $html): string
    {
        return html_entity_decode(
            trim(
                strip_tags(
                    preg_replace(
                        '/<(head|title|style|script)[^>]*>.*?<\/\\1>/si',
                        '',
                        $html
                    )
                )
            ),
            ENT_QUOTES,
            'iso-8859-1'
        );
    }
}
