<?php

declare(strict_types=1);

namespace Xentral\Modules\SystemMailer;

use Exception;
use Xentral\Components\Logger\LoggerAwareTrait;
use Xentral\Components\Mailer\Data\EmailMessage;
use Xentral\Components\Mailer\Data\FileAttachment;
use Xentral\Components\Mailer\Exception\MailerTransportException;
use Xentral\Components\Mailer\Mailer;
use Xentral\Modules\GoogleApi\Exception\AuthorizationExpiredException;
use Xentral\Modules\SystemMailer\Data\EmailBackupAccount;
use Xentral\Modules\SystemMailer\Service\EmailAccountGateway;
use Xentral\Modules\SystemMailer\Service\MailBodyCleaner;
use Xentral\Modules\SystemMailer\Service\MailerTransportFactory;
use Xentral\Modules\SystemMailer\Service\MailLogService;

final class SystemMailer
{
    use LoggerAwareTrait;

    /** @var MailerTransportFactory $factory */
    private $factory;

    /** @var MailLogService $emailLog */
    private $emailLog;

    /** @var MailBodyCleaner $cleaner */
    private $cleaner;

    /** @var EmailAccountGateway $accountGateway */
    private $accountGateway;

    /**
     * @param MailerTransportFactory $factory
     * @param EmailAccountGateway    $accountGateway
     * @param MailLogService         $emailLog
     * @param MailBodyCleaner        $cleaner
     */
    public function __construct(
        MailerTransportFactory $factory,
        EmailAccountGateway $accountGateway,
        MailLogService $emailLog,
        MailBodyCleaner $cleaner
    ) {
        $this->factory = $factory;
        $this->emailLog = $emailLog;
        $this->cleaner = $cleaner;
        $this->accountGateway = $accountGateway;
    }

    /**
     * @param EmailMessage       $email
     * @param EmailBackupAccount $account
     *
     * @return bool success
     */
    public function send(EmailMessage $email, EmailBackupAccount $account): bool
    {
        $transport = $this->factory->createMailerTransport($account);
        $email = $this->cleaner->cleanEmailBody($email);
        $mailer = new Mailer($transport);
        try {
            $success = $mailer->send($email);
            $this->emailLog->logOutgoingMail($email, $account, $transport->getStatus());
            if ($transport->hasErrors()) {
                $this->logger->error('Error while sending email.', ['error' => $transport->getErrorMessages()]);
            }
        } catch (MailerTransportException $e) {
            $this->logger->error($e->getMessage(), ['dump' => $transport->getErrorMessages()]);

            return false;
        } catch (AuthorizationExpiredException $e) {
            $this->logger->error(
                'Sending Email Failed: Google authorization expired',
                ['exception' => $e]
            );

            return false;
        }

        return $success;
    }

    /**
     * @deprecated only for backwards compatibility with erpApi::MailSendFinal
     *
     * @param string $senderEmail
     * @param string $senderName
     * @param array  $recipients
     * @param string $subject
     * @param string $body
     * @param array  $attachFiles
     * @param array  $ccs
     * @param array  $bccs
     *
     * @return bool success
     */
    public function composeAndSendEmail(
        $senderEmail,
        $senderName,
        $recipients,
        $subject,
        $body,
        $attachFiles = [],
        $ccs = [],
        $bccs = []
    ): bool {
        //cannot use Mailer component if no emailbackup account exists
        $account = null;
        try {
            $account = $this->accountGateway->getAccountByEmail($senderEmail);
        } catch (Exception $e) {
            $account = null;
            $this->logger->error($e->getMessage(), ['exception' => $e]);
        }

        if ($account === null) {
            return false;
        }

        //use Mailer component only for 'smtp' or 'gmail' authtype
        if (
            $account->isSmtpEnabled() === false
            || (
                $account->getSmtpAuthType() !== EmailBackupAccount::AUTH_SMTP
                && $account->getSmtpAuthType() !== EmailBackupAccount::AUTH_GMAIL
            )
        ) {
            return false;
        }

        $email = new EmailMessage($subject, $body, $recipients, $ccs, $bccs);
        foreach ($attachFiles as $file) {
            if ($file !== null && file_exists($file)) {
                $email->addAttachment(new FileAttachment($file));
            }
        }

        $account = $account
            ->withSmtpSenderName($senderName)
            ->withDisplayName($senderName);

        //send email with new Mailer component
        return $this->send($email, $account);
    }

    /**
     * @deprecated only for backwards compatibility with erpApi::MailSendFinal
     *
     * @param array  $recipients
     * @param string $subject
     * @param string $body
     * @param array  $attachFiles
     * @param array  $ccs
     * @param array  $bccs
     *
     * @return EmailMessage
     */
    public function composeEmail(
        $recipients,
        $subject,
        $body,
        $attachFiles = [],
        $ccs = [],
        $bccs = []
    ): EmailMessage {
        $email = new EmailMessage($subject, $body, $recipients, $ccs, $bccs);
        foreach ($attachFiles as $file) {
            if ($file !== null && file_exists($file)) {
                $email->addAttachment(new FileAttachment($file));
            }
        }

        return $email;
    }
}
