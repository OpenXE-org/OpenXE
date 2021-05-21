<?php

declare(strict_types=1);

namespace Xentral\Components\MailClient\Data;

use JsonSerializable;
use Xentral\Components\MailClient\Exception\InvalidArgumentException;
use Xentral\Components\Util\StringUtil;

final class MailMessagePartData implements MailMessagePartInterface, JsonSerializable
{
    /** @var MailMessageHeaderValue[] $headers */
    private $headers;

    /** @var MailMessagePartInterface[] $parts */
    private $parts;

    /** @var string|null $content */
    private $content;

    /**
     * @param array       $headers
     * @param string|null $content
     * @param array       $parts
     */
    public function __construct(
        array $headers,
        ?string $content,
        array $parts
    ) {
        $headers = array_change_key_case($headers, CASE_LOWER);
        $this->headers = $headers;
        $this->content = $content;
        $this->parts = [];
        foreach ($parts as $part) {
            $this->addPart($part);
        }
    }

    /**
     * @param array $data
     *
     * @throws InvalidArgumentException
     *
     * @return MailMessagePartData
     */
    public static function fromJsonArray(array $data): MailMessagePartData
    {
        if (!array_key_exists('headers', $data)) {
            throw new InvalidArgumentException('Headers required');
        }
        if (!array_key_exists('content', $data)) {
            throw new InvalidArgumentException('content required');
        }
        if (!array_key_exists('parts', $data)) {
            throw new InvalidArgumentException('Message parts required');
        }

        $headers = [];
        foreach ($data['headers'] as $header) {
            $headerValue = MailMessageHeaderValue::fromJsonArray($header);
            $headers[$headerValue->getName()] = $headerValue;
        }

        $part = new self($headers, $data['content'], []);
        foreach ($data['parts'] as $subPart) {
            $part->addPart(self::fromJsonArray($subPart));
        }

        return $part;
    }

    /**
     * @inheritDoc
     */
    public function getHeader(string $name): ?MailMessageHeaderValue
    {
        if (!isset($this->headers[strtolower($name)])) {
            return null;
        }

        return $this->headers[strtolower($name)];
    }

    /**
     * @inheritDoc
     */
    public function getContentType(): string
    {
        $header = $this->getHeader('content-type');
        if ($header === null) {
            return '';
        }
        $split = preg_split('/;/', $header->getValue(), -1, 0);

        return $split[0];
    }

    /**
     * @inheritDoc
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @inheritDoc
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * @return string|null
     */
    public function getDecodedContent(): ?string
    {
        if ($this->content === null) {
            return null;
        }
        $encodingHeader = $this->getHeader('content-transfer-encoding');
        if ($encodingHeader === null ) {
            return $this->content;
        }

        return $this->decode($this->content, $encodingHeader->getValue());
    }

    /**
     * @inheritDoc
     */
    public function getPart(int $index): MailMessagePartInterface
    {
        return $this->parts[$index];
    }

    /**
     * @inheritDoc
     */
    public function countParts(): int
    {
        return count($this->parts);
    }

    /**
     * @return bool
     */
    public function isMultipart(): bool
    {
        $contentType = $this->getContentType();
        if ($contentType === null) {
            return false;
        }

        return StringUtil::startsWith($contentType, 'multipart/');
    }

    /**
     * @param MailMessagePartInterface $part
     */
    private function addPart(MailMessagePartInterface $part): void
    {
        $this->parts[] = $part;
    }

    /**
     * @param string $content
     * @param string $encoding
     *
     * @return string
     */
    private function decode(string $content, string $encoding): string
    {
        switch (strtolower($encoding)) {
            case 'quoted-printable':
                return quoted_printable_decode($content);
                //no break

            case 'base64':
                return base64_decode($content);
                //no break

            // default includes 7bit, 8bit and binary
            default:
                return $content;
        }
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'headers' => $this->headers,
            'content' => $this->content,
            'parts' => $this->parts,
        ];
    }
}
