<?php

declare(strict_types=1);

namespace Xentral\Components\MailClient\Data;

use JsonSerializable;
use Xentral\Components\MailClient\Exception\InvalidArgumentException;

final class MailMessageHeaderValue implements MailMessageHeaderInterface, JsonSerializable
{
    /** @var string $name */
    private $name;

    /** @var string $value */
    private $value;

    /**  @var string $encoding */
    private $encoding;

    /**
     * @param string $name
     * @param string $value
     * @param string $encoding
     */
    public function __construct(string $name, string $value, string $encoding)
    {
        $this->name = $name;
        $this->value = $value;
        $this->encoding = $encoding;
    }

    /**
     * @param array $data
     *
     * @throws InvalidArgumentException
     *
     * @return MailMessageHeaderValue
     */
    public static function fromJsonArray(array $data): MailMessageHeaderValue
    {
        if (!isset($data['name'], $data['value'], $data['encoding'])) {
            throw new InvalidArgumentException('Header incomplete');
        }

        return new self($data['name'], $data['value'], $data['encoding']);
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
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getEncoding(): string
    {
        return $this->encoding;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'name'     => $this->name,
            'value'    => $this->value,
            'encoding' => $this->encoding,
        ];
    }
}
