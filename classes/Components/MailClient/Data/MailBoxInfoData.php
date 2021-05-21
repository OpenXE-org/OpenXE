<?php

declare(strict_types=1);

namespace Xentral\Components\MailClient\Data;

final class MailBoxInfoData
{
    /** @var int $messages */
    private $messages;

    /** @var int $recent */
    private $recent;

    /** @var int $uidvalidity */
    private $uidvalidity;

    /** @var array $flags */
    private $flags;

    /**
     * @param int   $messages
     * @param int   $recent
     * @param int   $uidvalidity
     * @param array $flags
     */
    public function __construct(
        int $messages,
        int $recent,
        int $uidvalidity,
        array $flags = []
    )
    {
        $this->messages = $messages;
        $this->recent = $recent;
        $this->uidvalidity = $uidvalidity;
        $this->flags = $flags;
    }

    /**
     * @return int total amount of messages
     */
    public function getMessages(): int
    {
        return $this->messages;
    }

    /**
     * @return int amount of recent messages
     */
    public function getRecentMessages(): int
    {
        return $this->recent;
    }

    /**
     * @return int
     */
    public function getUidvalidity(): int
    {
        return $this->uidvalidity;
    }

    /**
     * @return array
     */
    public function getFlags(): array
    {
        return $this->flags;
    }

    /**
     * @param string $flag
     *
     * @return bool
     */
    public function hasFlag(string $flag): bool
    {
        return array_key_exists($flag, $this->flags);
    }
}
