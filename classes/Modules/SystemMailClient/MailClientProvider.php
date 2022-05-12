<?php

declare(strict_types=1);

namespace Xentral\Modules\SystemMailClient;

use Xentral\Components\MailClient\Client\MailClientInterface;
use Xentral\Components\MailClient\MailClientFactory;
use Xentral\Modules\SystemMailClient\Exception\EmailAccountNotFoundException;
use Xentral\Modules\SystemMailClient\Exception\MailClientConfigException;
use Xentral\Modules\SystemMailClient\Exception\OAuthException;
use Xentral\Modules\SystemMailer\Data\EmailBackupAccount;
use Xentral\Modules\SystemMailer\Exception\EmailBackupAccountException;
use Xentral\Modules\SystemMailer\Service\EmailAccountGateway;

final class MailClientProvider implements MailClientProviderInterface
{
    /** @var MailClientFactory */
    private $factory;

    /** @var MailClientConfigProvider */
    private $configProvider;

    /** @var EmailAccountGateway $accountGateway */
    private $accountGateway;

    /**
     * @param MailClientFactory        $factory
     * @param MailClientConfigProvider $configProvider
     * @param EmailAccountGateway      $accountGateway
     */
    public function __construct(
        MailClientFactory $factory,
        MailClientConfigProvider $configProvider,
        EmailAccountGateway $accountGateway
    ) {
        $this->factory = $factory;
        $this->configProvider = $configProvider;
        $this->accountGateway = $accountGateway;
    }

    /**
     * @param int $userId
     *
     * @throws EmailAccountNotFoundException
     * @throws MailClientConfigException
     * @throws OAuthException
     *
     * @return MailClientInterface
     */
    public function createMailClientByUserId(int $userId): MailClientInterface
    {
        try {
            $account = $this->accountGateway->getAccountByUser($userId);
        } catch (EmailBackupAccountException $e) {
            throw new EmailAccountNotFoundException($e->getMessage(), $e->getCode(), $e);
        }

        return $this->createMailClientFromAccount($account);
    }

    /**
     * @param int $addressId
     *
     * @throws EmailAccountNotFoundException
     * @throws MailClientConfigException
     * @throws OAuthException
     *
     * @return MailClientInterface
     */
    public function createMailClientByAddressId(int $addressId): MailClientInterface
    {
        try {
            $account = $this->accountGateway->getAccountByAddress($addressId);
        } catch (EmailBackupAccountException $e) {
            throw new EmailAccountNotFoundException($e->getMessage(), $e->getCode(), $e);
        }

        return $this->createMailClientFromAccount($account);
    }

    /**
     * @param string $emailAddress
     *
     * @throws EmailAccountNotFoundException
     * @throws MailClientConfigException
     * @throws OAuthException
     *
     * @return MailClientInterface
     */
    public function createMailClientByEmail(string $emailAddress): MailClientInterface
    {
        try {
            $account = $this->accountGateway->getAccountByEmail($emailAddress);
        } catch (EmailBackupAccountException $e) {
            throw new EmailAccountNotFoundException($e->getMessage(), $e->getCode(), $e);
        }

        return $this->createMailClientFromAccount($account);
    }

    /**
     * @param EmailBackupAccount $account
     *
     * @throws OAuthException
     * @throws MailClientConfigException
     *
     * @return MailClientInterface
     */
    public function createMailClientFromAccount(EmailBackupAccount $account): MailClientInterface
    {
        switch ($account->getImapType()) {
            case 1:
                // NO BREAK
            case 3:
                // NO BREAK
            case 5:
                $config = $this->configProvider->createImapConfigFromAccount($account);

                return $this->factory->createImapClient($config);
        }

        throw new MailClientConfigException(sprintf('Unrecognized mail client type "%s"', $account->getImapType()));
    }
}
