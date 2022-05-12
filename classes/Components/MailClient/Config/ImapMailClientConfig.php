<?php

declare(strict_types=1);

namespace Xentral\Components\MailClient\Config;

final class ImapMailClientConfig implements ImapMailClientConfigInterface
{
    /** @var string $server */
    private $server;

    /** @var int $port */
    private $port;

    /** @var string $user */
    private $user;

    /** @var string $password */
    private $password;

    /** @var string $authType */
    private $authType;

    /** @var bool $sslEnabled */
    private $sslEnabled;

    /** @var string $folder */
    private $folder;

    /**
     * @param string      $server
     * @param int         $port
     * @param string      $user
     * @param string      $password
     * @param string      $authType
     * @param bool        $sslEnabled
     * @param string|null $folder
     */
    public function __construct(
        string $server,
        int $port,
        string $user,
        string $password,
        string $authType = self::AUTH_BASIC,
        bool $sslEnabled = true,
        string $folder = 'INBOX'
    ) {
        $this->server = $server;
        $this->port = $port;
        $this->folder = $folder;
        $this->user = $user;
        $this->password = $password;
        $this->authType = $authType;
        $this->sslEnabled = $sslEnabled;
    }

    /**
     * @return string
     */
    public function getServer(): string
    {
        return $this->server;
    }

    /**
     * @return int
     */
    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * @return string
     */
    public function getUser(): string
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getAuthType(): string
    {
        return $this->authType;
    }

    /**
     * @return bool
     */
    public function isSslEnabled(): bool
    {
        return $this->sslEnabled;
    }

    /**
     * @return string
     */
    public function getInboxFolder(): string
    {
        return $this->folder;
    }
}
