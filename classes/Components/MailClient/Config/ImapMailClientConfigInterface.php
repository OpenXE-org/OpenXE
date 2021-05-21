<?php

declare(strict_types=1);

namespace Xentral\Components\MailClient\Config;

interface ImapMailClientConfigInterface
{
    /** @var string AUTH_BASIC */
    public const AUTH_BASIC = 'basic';

    /** @var string AUTH_XOAUTH2 */
    public const AUTH_XOAUTH2 = 'xoauth2';

    /**
     * @return string
     */
    public function getServer(): string;

    /**
     * @return int
     */
    public function getPort(): int;

    /**
     * @return string
     */
    public function getUser(): string;

    /**
     * @return string
     */
    public function getPassword(): string;

    /**
     * @return string
     */
    public function getAuthType(): string;

    /**
     * @return bool
     */
    public function isSslEnabled(): bool;

    /**
     * @return string
     */
    public function getInboxFolder(): string;
}
