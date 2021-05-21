<?php

declare(strict_types=1);

namespace Xentral\Components\MailClient\Data;

interface MailMessageHeaderInterface
{
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return string
     */
    public function getValue(): string;

    /**
     * @return string
     */
    public function getEncoding(): string;
}
