<?php

declare(strict_types=1);

namespace Xentral\Components\MailClient\Client;

use Laminas\Mail\Header\ContentType;
use Laminas\Mail\Header\MessageId;
use Laminas\Mail\Message;
use Laminas\Mime\Message as MimeMessage;
use Laminas\Mime\Mime;
use Laminas\Mime\Part;
use Xentral\Components\MailClient\Exception\InvalidArgumentException;
use Xentral\Components\MailClient\Exception\MessageFormatException;
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
    public function formatMessage(EmailMessage $email, EmailRecipient $from, string $messageId = null): string
    {
        if ($messageId !== null && !preg_match('/^<.*@.*>$/', $messageId)) {
            throw new InvalidArgumentException('message id must be RFC 5322 conform');
        }
        $message = new Message();
        $message->setEncoding('UTF-8');
        $message->addFrom($from->getEmail(), $from->getName());
        foreach ($email->getRecipients() as $recipient) {
            $message->addTo($recipient->getEmail(), $recipient->getName());
        }
        foreach ($email->getCcRecipients() as $recipient) {
            $message->addCc($recipient->getEmail(), $recipient->getName());
        }
        foreach ($email->getBccRecipients() as $recipient) {
            $message->addBcc($recipient->getEmail(), $recipient->getName());
        }
        $message->setSubject($email->getSubject());
        $message->addReplyTo($from->getEmail(), $from->getName());
        $idHeader = new MessageId();
        $idHeader->setId($messageId);
        $message->getHeaders()->addHeader($idHeader);
        $body = $this->createMessageBody($email);
        $message->setBody($body);

        $contentType = null;
        if (count($email->getAttachments()) > 0) {
            $contentType = Mime::MULTIPART_RELATED;
        }
        if (count($email->getAttachments()) === 0 && $email->isHtml()) {
            $contentType = Mime::MULTIPART_ALTERNATIVE;
        }
        if ($contentType !== null) {
            /** @var ContentType $contentTypeHeader */
            $contentTypeHeader = $message->getHeaders()->get('Content-Type');
            $contentTypeHeader->setType($contentType);
        }

        return $message->toString();
    }

    /**
     * @param EmailMessage $email
     *
     * @return MimeMessage
     */
    private function createMessageBody(EmailMessage $email): MimeMessage
    {
        $textParts = [];
        if (!$email->isHtml()) {
            $plainText = $email->getBody();
        } else {
            $plainText = $this->convertHtmlToPlainText($email->getBody());
        }
        $textPart = new Part(Mime::encode($plainText, Mime::ENCODING_8BIT));
        $textPart->type = Mime::TYPE_TEXT;
        $textPart->charset = 'ISO-8859-1';
        $textPart->encoding = Mime::ENCODING_QUOTEDPRINTABLE;
        if ($email->isHtml()) {
            $htmlPart = new Part($email->getBody());
            $htmlPart->type = Mime::TYPE_HTML;
            $htmlPart->charset = 'ISO-8859-1';
            $htmlPart->encoding = Mime::ENCODING_QUOTEDPRINTABLE;
            $textParts[] = $htmlPart;
        }
        $textParts[] = $textPart;
        $attachmentParts = [];
        foreach ($email->getAttachments() as $attachment) {
            $contentStream = null;
            $disposition = null;
            switch (get_class($attachment)) {
                case FileAttachment::class:
                    $content = file_get_contents($attachment->getPath());
                    $disposition = Mime::DISPOSITION_ATTACHMENT;
                    break;

                case StringAttachment::class:
                    /** @var StringAttachment $attachment */
                    $content = $attachment->getContent();
                    $disposition = Mime::DISPOSITION_ATTACHMENT;
                    break;

                case ImageAttachment::class:
                    $content = file_get_contents($attachment->getPath());
                    $disposition = Mime::DISPOSITION_INLINE;
                    break;

                default:
                    throw new MessageFormatException(
                        sprintf('unrecognized attachment class "%s"', get_class($attachment))
                    );
            }
            $part = new Part($content);
            $part->disposition = $disposition;
            $part->type = $attachment->getType();
            $part->filename = $attachment->getName();
            $part->encoding = $attachment->getEncoding();
            $attachmentParts[] = $part;
        }
        $attachmentParts = array_merge($textParts, $attachmentParts);
        $body = new MimeMessage();
        $body->setParts($attachmentParts);

        return $body;
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
