<?php

declare(strict_types=1);

namespace Xentral\Components\MailClient\Data;

use Xentral\Components\MailClient\Exception\InvalidArgumentException;

class MailAttachmentData implements MailAttachmentInterface
{
    /** @var string $filename */
    private $filename;

    /** @var string $content */
    private $content;

    /** @var string $contentType */
    private $contentType;

    /** @var string $encoding */
    private $encoding;

    /** @var bool $isInlineAttachment*/
    private $isInlineAttachment;

    /** @var string|null $cid */
    private $cid;

    /**
     * @param string      $filename
     * @param string      $content
     * @param string      $contentType
     * @param string      $encoding
     * @param bool        $isInlineAttachment
     * @param string|null $cid
     */
    public function __construct(
        string $filename,
        string $content,
        string $contentType,
        string $encoding,
        bool $isInlineAttachment = false,
        string $cid = null
    )
    {
        $this->filename = $filename;
        $this->content = $content;
        $this->contentType = $contentType;
        $this->encoding = $encoding;
        $this->isInlineAttachment = $isInlineAttachment;
        $this->cid = $cid;
    }

    /**
     * @param MailMessagePartInterface $part
     *
     * @throws InvalidArgumentException
     *
     * @return MailAttachmentData
     */
    public static function fromMailMessagePart(MailMessagePartInterface $part): MailAttachmentData
    {
        $encodingHeader = $part->getHeader('content-transfer-encoding');
        if ($encodingHeader === null) {
            throw new InvalidArgumentException('missing header: "Content-Transfer-Encoding"');
        }
        $encoding = $encodingHeader->getValue();
        $dispositionHeader = $part->getHeader('content-disposition');
        if ($dispositionHeader === null) {
            throw new InvalidArgumentException('missing header: "Content-Disposition"');
        }
        $disposition = $dispositionHeader->getValue();
        if (!preg_match('/(.+);\s*filename="([^"]+)".*$/m', $disposition, $matches)) {
            throw new InvalidArgumentException(
                sprintf('unexpected header value "Content-Disposition" = %s', $disposition)
            );
        }
        $isInline = strtolower($matches[1]) === 'inline';
        $filename = $matches[2];
        $cid = null;
        $contentIdHeader = $part->getHeader('content-id');
        if ($contentIdHeader !== null) {
            $cid = $contentIdHeader->getValue();
            if (preg_match('/[<]?([^<>]+)[>]?$/', $cid, $cidMatches)) {
                $cid = $cidMatches[1];
            }
        }

        return new self(
            $filename,
            $part->getContent(),
            $part->getContentType(),
            $encoding,
            $isInline,
            $cid
        );
    }

    /**
     * @return string
     */
    public function getFileName(): string
    {
        return $this->filename;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        switch ($this->encoding) {
            case 'base64':
                return base64_decode($this->content);

            default:
                return $this->content;
        }
    }

    /**
     * @return string
     */
    public function getContentType(): string
    {
        return $this->contentType;
    }

    /**
     * @return string
     */
    public function getTransferEncoding(): string
    {
        return $this->encoding;
    }

    /**
     * @return bool
     */
    public function isInlineAttachment(): bool
    {
        return $this->isInlineAttachment;
    }

    /**
     * @return string|null
     */
    public function getCid(): ?string
    {
        return $this->cid;
    }
}
