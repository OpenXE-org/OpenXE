<?php

declare(strict_types=1);

namespace Xentral\Components\Mailer\Data;

final class StringAttachment implements EmailAttachmentInterface
{
    /** @var string $content*/
    private $content;

    /** @var string $name*/
    private $name;

    /** @var string $encoding */
    private $encoding;

    /** @var string $type */
    private $type;

    /** @var string $disposition */
    private $disposition;

    /**
     * @param string $content
     * @param string $name
     * @param string $encoding
     * @param string $type
     * @param string $disposition
     */
    public function __construct(
        string $content,
        string $name,
        string $encoding = self::ENCODING_BASE64,
        string $type = 'application/octet-stream',
        string $disposition = self::DISPOSITION_ATTACHMENT
    )
    {
        $this->content = $content;
        $this->name = $name;
        $this->encoding = $encoding;
        $this->type = $type;
        $this->disposition = $disposition;
    }

    /**
     * @return string
     */
    public function getContent():string
    {
        return $this->content;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return '';
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getEncoding(): string
    {
        return $this->encoding;
    }

    /**
     * @return string
     */
    public function getDisposition(): string
    {
        return $this->disposition;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }
}
