<?php

declare(strict_types=1);

namespace Xentral\Components\MailClient\Client;

use Xentral\Components\MailClient\Data\MailBoxInfoData;
use Xentral\Components\MailClient\Data\MailMessageInterface;

interface MailClientInterface
{
    /**
     * @return void
     */
    public function connect(): void;

    /**
     * @return void
     */
    public function disconnect(): void;

    /**
     * @param string $criteria
     *
     * @return array
     */
    public function searchMessages(string $criteria): array;

    /**
     * @param int $msgNumber
     *
     * @return MailMessageInterface
     */
    public function fetchMessage(int $msgNumber): MailMessageInterface;

    /**
     * @param int    $msgNumber
     * @param string $targetMailbox
     *
     * @return void
     */
    public function copyMessage(int $msgNumber, string $targetMailbox): void;

    /**
     * @param int $msgNumber
     *
     * @return void
     */
    public function deleteMessage(int $msgNumber): void;

    /**
     * @param string $folder
     *
     * @return void
     */
    public function selectFolder(string $folder): void;

    /**
     * @param string $inbox
     *
     * @return MailBoxInfoData
     */
    public function examineInbox(string $inbox): MailBoxInfoData;

    /**
     * @return bool
     */
    public function expunge(): bool;

    /**
     * @param int      $msgNumber
     * @param string[] $flags
     *
     * @return void
     */
    public function setFlags(int $msgNumber, array $flags): void;

    /**
     * @return void
     */
    public function noop(): void;
}
