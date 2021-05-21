<?php

declare(strict_types=1);

namespace Xentral\Modules\SystemMailer\Service;

use PHPMailer\PHPMailer\PHPMailer;
use Xentral\Components\Logger\LoggerAwareTrait;
use Xentral\Components\Mailer\Config\OAuthMailerConfig;
use Xentral\Components\Mailer\Config\SmtpMailerConfig;
use Xentral\Components\Mailer\Transport\MailerTransportInterface;
use Xentral\Components\Mailer\Transport\PhpMailerOAuth;
use Xentral\Components\Mailer\Transport\PhpMailerTransport;
use Xentral\Core\DependencyInjection\ContainerInterface;
use Xentral\Modules\GoogleApi\Exception\GoogleAccountNotFoundException;
use Xentral\Modules\GoogleApi\Exception\GoogleCredentialsException;
use Xentral\Modules\GoogleApi\Service\GoogleAccountGateway;
use Xentral\Modules\GoogleApi\Service\GoogleAuthorizationService;
use Xentral\Modules\GoogleApi\Service\GoogleCredentialsService;
use Xentral\Modules\SystemMailer\Data\EmailBackupAccount;
use Xentral\Modules\SystemMailer\Exception\GmailOAuthException;
use Xentral\Modules\SystemMailer\Exception\InvalidArgumentException;
use Xentral\Modules\SystemMailer\Transport\PhpMailerGoogleAuthentification;

class MailerTransportFactory
{
    use LoggerAwareTrait;

    /** @var ContainerInterface $container */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    /**
     * @param EmailBackupAccount $account
     *
     * @throws InvalidArgumentException
     *
     * @return MailerTransportInterface
     */
    public function createMailerTransport(EmailBackupAccount $account):MailerTransportInterface
    {
        switch ($account->getSmtpAuthType()) {

            case EmailBackupAccount::AUTH_SMTP:
                return $this->createSmtpTransport($account);
                break;

            case EmailBackupAccount::AUTH_GMAIL:
                return $this->createGoogleOAuthTransport($account);

            default:
                throw new InvalidArgumentException('Only SMTP accounts are supported.');
        }
    }

    /**
     * @param EmailBackupAccount $account
     *
     * @return SmtpMailerConfig
     */
    public function createSmtpMailerConfig(EmailBackupAccount $account):SmtpMailerConfig
    {
        if ($account->isSmtpEnabled() === false || $account->getSmtpAuthType() !== EmailBackupAccount::AUTH_SMTP) {
            throw new InvalidArgumentException('Only SMTP accounts are supported.');
        }

        $email = $account->getSmtpSenderEmail();
        if ($email === '') {
            $email = $account->getEmailAddress();
        }
        $sender = $account->getSmtpSenderName();
        if ($sender === '') {
            $sender = $account->getDisplayName();
        }
        $debug = 0;
        if ($account->isSmtpDebugEnabled()) {
            $debug = 4;
        }

        return new SmtpMailerConfig([
            'sender_email'  => $email,
            'sender_name'   => $sender,
            'host'          => $account->getSmtpServer(),
            'hostname'      => $account->getClientAlias(),
            'username'      => $account->getUserName(),
            'password'      => $account->getPassword(),
            'port'          => $account->getSmtpPort(),
            'smtp_security' => $account->getSmtpSecurity(),
            'mailer'        => 'smtp',
            'smtp_debug'    => $debug
        ]);
    }

    /**
     * @param EmailBackupAccount $account
     *
     * @throws InvalidArgumentException
     *
     * @return OAuthMailerConfig
     */
    public function createGmailMailerConfig(EmailBackupAccount $account):OAuthMailerConfig
    {
        if (
            $account->isSmtpEnabled() === false
            || $account->getSmtpAuthType() !== EmailBackupAccount::AUTH_GMAIL
        ) {
            throw new InvalidArgumentException('Only SMTP OAUTH accounts are supported.');
        }
        $email = $account->getSmtpSenderEmail();
        if ($email === '') {
            $email = $account->getEmailAddress();
        }
        $sender = $account->getSmtpSenderName();
        if ($sender === '') {
            $sender = $account->getDisplayName();
        }
        $debug = 0;
        if ($account->isSmtpDebugEnabled()) {
            $debug = 4;
        }
        $cfgValues = [
            'sender_email'  => $email,
            'sender_name'   => $sender,
            'host'          => $account->getSmtpServer(),
            'hostname'      => $account->getClientAlias(),
            'port'          => $account->getSmtpPort(),
            'smtp_security' => $account->getSmtpSecurity(),
            'mailer'        => 'smtp',
            'smtp_debug'    => $debug
        ];
        if ($cfgValues['host'] === '') {
            $cfgValues['host'] = 'smtp.gmail.com';
        }

        return new OAuthMailerConfig($cfgValues);
    }

    /**
     * @param EmailBackupAccount $account
     *
     * @return PhpMailerTransport
     */
    public function createSmtpTransport(EmailBackupAccount $account):PhpMailerTransport
    {
        $config = $this->createSmtpMailerConfig($account);
        $mailer = new PHPMailer(true);

        return new PhpMailerTransport($mailer, $config, $this->logger);
    }

    /**
     * @param EmailBackupAccount $account
     *
     * @throws GoogleCredentialsException
     * @throws GmailOAuthException
     * @throws GoogleAccountNotFoundException
     *
     * @return PhpMailerTransport
     */
    public function createGoogleOAuthTransport(EmailBackupAccount $account):PhpMailerTransport
    {
        $config = $this->createGmailMailerConfig($account);
        /** @var GoogleCredentialsService $credentialsService */
        $credentialsService = $this->container->get('GoogleCredentialsService');
        $credentialsService->getCredentials()->validate();
        /** @var GoogleAccountGateway $googleApiGateway */
        $googleApiGateway = $this->container->get('GoogleAccountGateway');
        $googleAccount = $googleApiGateway->getAccountByGmailAddress($account->getSenderEmailAddress());

        /** @var GoogleAuthorizationService $googleAuth */
        $googleAuth = $this->container->get('GoogleAuthorizationService');
        $oauth = new PhpMailerGoogleAuthentification(
            $googleApiGateway,
            $googleAuth,
            $googleAccount
        );
        $mailer = new PhpMailerOAuth(true, $oauth);

        return new PhpMailerTransport($mailer, $config, $this->logger);
    }
}
