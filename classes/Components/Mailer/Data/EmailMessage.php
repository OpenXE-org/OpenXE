<?php

declare(strict_types=1);

namespace Xentral\Components\Mailer\Data;

final class EmailMessage
{
    /** @var EmailRecipient[] $recipients */
    private $recipients;

    /** @var string $subject */
    private $subject;

    /** @var string $body */
    private $body;

    /** @var EmailRecipient[] $ccRecipients */
    private $ccRecipients;

    /** @var EmailRecipient[] $bccRecipients */
    private $bccRecipients;

    /** @var EmailAttachmentInterface[] $attachments */
    private $attachments;

    /** @var array $headers */
    private $headers;

    /**
     * @param string                          $subject
     * @param string                          $body
     * @param EmailRecipient[]|null           $recipients
     * @param EmailRecipient[]|null           $ccRecipients
     * @param EmailRecipient[]|null           $bccRecipients
     * @param EmailAttachmentInterface[]|null $attachments
     */
    public function __construct(
        $subject,
        $body,
        array $recipients = null,
        array $ccRecipients = null,
        array $bccRecipients = null,
        array $attachments = null
    ) {
        $this->recipients = [];
        $this->setRecipients($recipients);
        $this->subject = $subject;
        $this->body = $body;
        $this->setCcRecipients($ccRecipients);
        $this->setBccRecipients($bccRecipients);
        $this->setAttachments($attachments);
        $this->headers = [];
    }

    /**
     * @param EmailRecipient $recipient
     *
     * @return EmailMessage
     */
    public function addRecipient(EmailRecipient $recipient): EmailMessage
    {
        $this->recipients[] = $recipient;

        return $this;
    }

    /**
     * @param EmailRecipient $ccRecipient
     *
     * @return EmailMessage
     */
    public function addCcRecipient(EmailRecipient $ccRecipient): EmailMessage
    {
        $this->ccRecipients[] = $ccRecipient;

        return $this;
    }

    /**
     * @param EmailRecipient $bccRecipient
     *
     * @return EmailMessage
     */
    public function addBccRecipient(EmailRecipient $bccRecipient): EmailMessage
    {
        $this->bccRecipients[] = $bccRecipient;

        return $this;
    }

    /**
     * @param string $name
     * @param string $value
     *
     * @return EmailMessage
     */
    public function addCustomHeader($name, $value): EmailMessage
    {
        $this->headers[$name] = $value;

        return $this;
    }

    /**
     * @param EmailAttachmentInterface $attachment
     *
     * @return EmailMessage
     */
    public function addAttachment(EmailAttachmentInterface $attachment): EmailMessage
    {
        $this->attachments[] = $attachment;

        return $this;
    }

    /**
     * @param FileAttachment $attachment
     *
     * @return EmailMessage
     */
    public function addFileAttachment(FileAttachment $attachment): EmailMessage
    {
        $this->addAttachment($attachment);

        return $this;
    }

    /**
     * @param ImageAttachment $attachment
     *
     * @return EmailMessage
     */
    public function addEmbeddedImage(ImageAttachment $attachment): EmailMessage
    {
        $this->addAttachment($attachment);

        return $this;
    }

    /**
     * @param StringAttachment $attachment
     *
     * @return EmailMessage
     */
    public function addStringAttachment(StringAttachment $attachment): EmailMessage
    {
        $this->addAttachment($attachment);

        return $this;
    }

    /**
     * @return string
     */
    public function getRecipientsAsString(): string
    {
        return implode(';', $this->recipients);
    }

    /**
     * @return EmailRecipient[]
     *
     * @codeCoverageIgnore
     */
    public function getRecipients(): array
    {
        return $this->recipients;
    }

    /**
     * @return string
     *
     * @codeCoverageIgnore
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @return string
     *
     * @codeCoverageIgnore
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * @return string
     */
    public function getCcRecipientsAsString(): string
    {
        return implode(';', $this->ccRecipients);
    }

    /**
     * @return EmailRecipient[]
     *
     * @codeCoverageIgnore
     */
    public function getCcRecipients(): array
    {
        return $this->ccRecipients;
    }

    /**
     * @return string
     */
    public function getBccRecipientsAsString(): string
    {
        return implode(';', $this->bccRecipients);
    }

    /**
     * @return EmailRecipient[]
     *
     * @codeCoverageIgnore
     */
    public function getBccRecipients(): array
    {
        return $this->bccRecipients;
    }

    /**
     * @return array
     *
     * @codeCoverageIgnore
     */
    public function getCustomHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @return FileAttachment[]
     *
     * @codeCoverageIgnore
     */
    public function getAttachments(): array
    {
        return $this->attachments;
    }

    /**
     * @return bool
     */
    public function isHtml(): bool
    {
        return $this->body !== strip_tags($this->body);
    }

    /**
     * @param EmailRecipient[] $recipients
     *
     * @return void
     */
    private function setRecipients(array $recipients = null): void
    {
        $this->recipients = [];
        if ($recipients === null) {
            return;
        }
        foreach ($recipients as $item) {
            $this->addRecipient($item);
        }
    }

    /**
     * @param EmailRecipient[] $ccRecipients
     *
     * @return void
     */
    private function setCcRecipients(array $ccRecipients = null): void
    {
        $this->ccRecipients = [];
        if ($ccRecipients === null) {
            return;
        }
        foreach ($ccRecipients as $item) {
            $this->addCcRecipient($item);
        }
    }

    /**
     * @param EmailRecipient[] $bccRecipients
     *
     * @return void
     */
    private function setBccRecipients(array $bccRecipients = null): void
    {
        $this->bccRecipients = [];
        if ($bccRecipients === null) {
            return;
        }
        foreach ($bccRecipients as $item) {
            $this->addBccRecipient($item);
        }
    }

    /**
     * @param EmailAttachmentInterface[] $attachments
     *
     * @return void
     */
    private function setAttachments(array $attachments = null): void
    {
        $this->attachments = [];
        if ($attachments === null) {
            return;
        }
        foreach ($attachments as $item) {
            $this->addAttachment($item);
        }
    }
}
