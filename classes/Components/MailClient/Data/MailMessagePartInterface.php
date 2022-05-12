<?php

declare(strict_types=1);

namespace Xentral\Components\MailClient\Data;

interface MailMessagePartInterface
{
    /**
     * @return string
     */
    public function getContentType(): string;

    /**
     * @return bool
     */
    public function isMultipart(): bool;

    /**
     * @param string $name
     *
     * @return MailMessageHeaderValue|null
     */
    public function getHeader(string $name): ?MailMessageHeaderValue;

    /**
     * @return MailMessageHeaderValue[]|[]
     */
    public function getHeaders(): array;

    /**
     * @return string|null
     */
    public function getContent(): ?string;

    /**
     * @return string|null
     */
    public function getDecodedContent(): ?string;

    /**
     * @param int $index
     *
     * @return MailMessagePartInterface
     */
    public function getPart(int $index): MailMessagePartInterface;

    /**
     * @return int
     */
    public function countParts(): int;
}
