<?php

declare(strict_types=1);

namespace Xentral\Components\Logger\Context;

final class Origin implements OriginInterface
{
    /** @var string $type */
    private $type;

    /** @var string $payload */
    private $payload;

    /**
     * @param string $type
     * @param string $payload
     */
    public function __construct(string $type, string $payload)
    {
        $this->type = $type;
        $this->payload = $payload;
    }

    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @inheritDoc
     */
    public function getDetail(): string
    {
        return $this->payload;
    }
}
