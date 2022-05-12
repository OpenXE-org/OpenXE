<?php

declare(strict_types=1);

namespace Xentral\Components\MailClient\Data;

use DateTimeInterface;
use Xentral\Components\Mailer\Data\EmailRecipient;

interface MailMessageInterface extends MailMessagePartInterface
{
    /**
     * @return string[]
     */
    public function getFlags(): array;

    /**
     * @param string $flag
     *
     * @return bool
     */
    public function hasFlag(string $flag): bool;

    /**
     * @return string|null
     */
    public function getHtmlBody(): ?string;

    /**
     * @return string|null
     */
    public function getPlainTextBody(): ?string;

    /**
     * @return string|null
     */
    public function getRawContent(): ?string;

    /**
     * @return string|null
     */
    public function getSubject(): ?string;

    /**
     * @return DateTimeInterface|null
     */
    public function getDate(): ?DateTimeInterface;

    /**
     * @return EmailRecipient
     */
    public function getSender(): EmailRecipient;

    /**
     * @return string
     */
    public function getReplyToAddress(): string;

    /**
     * @return EmailRecipient[]
     */
    public function getRecipients(): array;

    /**
     * @return EmailRecipient[]
     */
    public function getCcRecipients(): array;

    /**
     * @return MailAttachmentInterface[]
     */
    public function getAttachments(): array;
}
