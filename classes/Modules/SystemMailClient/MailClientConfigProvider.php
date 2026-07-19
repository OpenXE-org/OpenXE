<?php

declare(strict_types=1);

namespace Xentral\Modules\SystemMailClient;

use Exception;
use Xentral\Components\MailClient\Config\ImapMailClientConfig;
use Xentral\Modules\GoogleApi\Client\GoogleApiClientFactory;
use Xentral\Modules\GoogleApi\Service\GoogleAccountGateway;
use Xentral\Modules\Office365Api\Service\Office365AccountGateway;
use Xentral\Modules\Office365Api\Service\Office365AuthorizationService;
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

    /** @var Office365AccountGateway|null $office365Gateway */
    private $office365Gateway;

    /** @var Office365AuthorizationService|null $office365AuthService */
    private $office365AuthService;

    /**
     * @param EmailAccountGateway                 $accountGateway
     * @param GoogleAccountGateway                $googleAccountGateway
     * @param GoogleApiClientFactory              $clientFactory
     * @param Office365AccountGateway|null        $office365Gateway
     * @param Office365AuthorizationService|null  $office365AuthService
     */
    public function __construct(
        EmailAccountGateway $accountGateway,
        GoogleAccountGateway $googleAccountGateway,
        GoogleApiClientFactory $clientFactory,
        ?Office365AccountGateway $office365Gateway = null,
        ?Office365AuthorizationService $office365AuthService = null
    )
    {
        $this->accountGateway = $accountGateway;
        $this->googleAccountGateway = $googleAccountGateway;
        $this->clientFactory = $clientFactory;
        $this->office365Gateway = $office365Gateway;
        $this->office365AuthService = $office365AuthService;
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

            // IMAP via Office365 OAuth
            case 6:
                return $this->createOffice365OauthImapConfig($account);
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

    /**
     * @param EmailBackupAccount $account
     *
     * @throws OAuthException
     *
     * @return ImapMailClientConfig
     */
    private function createOffice365OauthImapConfig(EmailBackupAccount $account): ImapMailClientConfig
    {
        if ($this->office365Gateway === null || $this->office365AuthService === null) {
            throw new OAuthException('Office365Api services are not available');
        }

        $email = $account->getEmailAddress();

        try {
            $office365Account = $this->office365Gateway->getAccountByEmailAddress($email);
            if ($office365Account === null) {
                throw new OAuthException('No Office365 account configured for ' . $email);
            }

            $token = $this->office365Gateway->getAccessToken($office365Account->getId());
            if ($token === null || $token->getTimeToLive() < 30) {
                $token = $this->office365AuthService->refreshAccessToken($office365Account);
            }
        } catch (Exception $e) {
            throw new OAuthException($e->getMessage(), $e->getCode(), $e);
        }

        $imapServer = $account->getImapServer();
        if ($imapServer === '') {
            $imapServer = 'outlook.office365.com';
        }
        $imapPort = $account->getImapPort();
        if ((int)$imapPort === 0) {
            $imapPort = 993;
        }

        return new ImapMailClientConfig(
            $imapServer,
            (int)$imapPort,
            $email,
            $token->getToken(),
            ImapMailClientConfig::AUTH_XOAUTH2,
            true,
            'INBOX'
        );
    }
}
