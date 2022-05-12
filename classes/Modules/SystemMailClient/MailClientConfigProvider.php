<?php

declare(strict_types=1);

namespace Xentral\Modules\SystemMailClient;

use Exception;
use Xentral\Components\MailClient\Config\ImapMailClientConfig;
use Xentral\Modules\GoogleApi\Client\GoogleApiClientFactory;
use Xentral\Modules\GoogleApi\Service\GoogleAccountGateway;
use Xentral\Modules\SystemMailClient\Exception\MailClientConfigException;
use Xentral\Modules\SystemMailClient\Exception\OAuthException;
use Xentral\Modules\SystemMailer\Data\EmailBackupAccount;
use Xentral\Modules\SystemMailer\Exception\EmailBackupAccountException;
use Xentral\Modules\SystemMailer\Service\EmailAccountGateway;

class MailClientConfigProvider
{
    /** @var EmailAccountGateway $accountGateway */
    private $accountGateway;

    /** @var GoogleAccountGateway $googleAccountGateway */
    private $googleAccountGateway;

    /** @var GoogleApiClientFactory $clientFactory */
    private $clientFactory;

    /**
     * @param EmailAccountGateway    $accountGateway
     * @param GoogleAccountGateway   $googleAccountGateway
     * @param GoogleApiClientFactory $clientFactory
     */
    public function __construct(
        EmailAccountGateway $accountGateway,
        GoogleAccountGateway $googleAccountGateway,
        GoogleApiClientFactory $clientFactory
    )
    {
        $this->accountGateway = $accountGateway;
        $this->googleAccountGateway = $googleAccountGateway;
        $this->clientFactory = $clientFactory;
    }

    /**
     * @param string $senderEmail
     *
     * @throws MailClientConfigException
     *
     * @return ImapMailClientConfig
     */
    public function createImapConfigFromEmail(string $senderEmail): ImapMailClientConfig
    {
        try {
            $account = $this->accountGateway->getAccountByEmail($senderEmail);

            return $this->createImapConfigFromAccount($account);
        } catch (EmailBackupAccountException $e) {}
        try {
            return $this->createGoogleOauthImapConfig($senderEmail);
        } catch (Exception $e) {
            throw new MailClientConfigException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param EmailBackupAccount $account
     *
     * @throws OAuthException
     *
     * @return ImapMailClientConfig
     */
    public function createImapConfigFromAccount(EmailBackupAccount $account): ImapMailClientConfig
    {
        $sslEnabled = false;
        switch ($account->getImapType()) {

            //IMAP with SSL
            case 3:
                $sslEnabled = true;
                break;

            // IMAP via Google OAuth
            case 5:
                return $this->createGoogleOauthImapConfig($account->getEmailAddress());
                break;

            // IMAP without SSL
            default:
                break;
        }

        return new ImapMailClientConfig(
            $account->getImapServer(),
            $account->getImapPort(),
            $account->getUserName(),
            $account->getPassword(),
            ImapMailClientConfig::AUTH_BASIC,
            $sslEnabled,
            'INBOX'
        );
    }

    /**
     * @param string $email
     *
     * @throws OAuthException
     *
     * @return ImapMailClientConfig
     */
    private function createGoogleOauthImapConfig(string $email): ImapMailClientConfig
    {
        $token = null;
        try {
            $account = $this->googleAccountGateway->getAccountByGmailAddress($email);
            $this->clientFactory->createClient($account->getUserId());
            $token = $this->googleAccountGateway->getAccessToken($account->getId());
        } catch (Exception $e) {
            throw new OAuthException($e->getMessage(), $e->getCode(), $e);
        }

        return new ImapMailClientConfig(
            'imap.gmail.com',
            993,
            $email,
            $token->getToken(),
            ImapMailClientConfig::AUTH_XOAUTH2,
            true,
            'INBOX'
        );
    }
}
