<?php

declare(strict_types=1);

namespace Xentral\Components\MailClient\Data;

interface MailAttachmentInterface
{
    /**
     * @return string
     */
    public function getFileName(): string;

    /**
     * @return string
     */
    public function getContent(): string;

    /**
     * @return string
     */
    public function getContentType(): string;

    /**
     * @return string
     */
    public function getTransferEncoding(): string;

    /**
     * @return bool
     */
    public function isInlineAttachment(): bool;

    /**
     * @return string|null
     */
    public function getCid(): ?string;
}
