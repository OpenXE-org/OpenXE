<?php

declare(strict_types=1);

namespace Xentral\Modules\SystemMailClient;

use Xentral\Components\MailClient\Client\MailClientInterface;
use Xentral\Modules\SystemMailer\Data\EmailBackupAccount;

interface MailClientProviderInterface
{
    /**
     * @param int $userId
     *
     * @return MailClientInterface
     */
    public function createMailClientByUserId(int $userId): MailClientInterface;

    /**
     * @param int $addressId
     *
     * @return MailClientInterface
     */
    public function createMailClientByAddressId(int $addressId): MailClientInterface;

    /**
     * @param string $emailAddress
     *
     * @return MailClientInterface
     */
    public function createMailClientByEmail(string $emailAddress): MailClientInterface;

    /**
     * @param EmailBackupAccount $account
     *
     * @return MailClientInterface
     */
    public function createMailClientFromAccount(EmailBackupAccount $account): MailClientInterface;
}
