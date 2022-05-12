<?php

declare(strict_types=1);

namespace Xentral\Components\Mailer\Data;

use SplFileInfo;
use Xentral\Components\Mailer\Exception\FileNotFoundException;

final class ImageAttachment implements EmailAttachmentInterface
{
    /** @var string TYPE_JPEG */
    const TYPE_JPEG = 'image/jpeg';

    /** @var string TYPE_PNG */
    const TYPE_PNG = 'image/png';

    /** @var string TYPE_GIF */
    const TYPE_GIF = 'image/gif';

    /** @var splFileInfo $file */
    private $file;

    /** @var string $cid */
    private $cid;

    /** @var string $name */
    private $name;

    /** @var string $encoding */
    private $encoding;

    /** @var string $type */
    private $type;

    /** @var string $disposition */
    private $disposition;

    /**
     * @param string      $path
     * @param string      $cid
     * @param string|null $name
     * @param string      $encoding
     * @param string      $type
     * @param string      $disposition
     */
    public function __construct(
        string $path,
        string $cid,
        string $name = null,
        string $encoding = self::ENCODING_BASE64,
        string $type = '',
        string $disposition = self::DISPOSITION_INLINE
    ) {
        $this->file = new splFileInfo($path);
        if (!$this->file->isFile()) {
            throw new FileNotFoundException(
                sprintf('Attachment File not found "%s".', $path)
            );
        }
        $this->cid = $cid;
        $this->name = $name === null ? $this->file->getFilename() : $name;
        $this->encoding = $encoding;
        $this->type = $type;
        $this->disposition = $disposition;
    }

    /**
     * @return string
     */
    public function getCid():string
    {
        return $this->cid;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->file->getRealPath();
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
    public function __toString():string
    {
        return (string)$this->file;
    }
}
