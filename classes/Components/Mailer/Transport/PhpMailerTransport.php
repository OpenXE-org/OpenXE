<?php

declare(strict_types=1);

namespace Xentral\Components\Mailer\Transport;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use Psr\Log\LoggerInterface;
use Xentral\Components\Mailer\Config\MailerConfigInterface;
use Xentral\Components\Mailer\Data\EmailMessage;
use Xentral\Components\Mailer\Data\FileAttachment;
use Xentral\Components\Mailer\Data\ImageAttachment;
use Xentral\Components\Mailer\Data\StringAttachment;
use Xentral\Components\Mailer\Exception\MailerTransportException;

final class PhpMailerTransport implements MailerTransportInterface
{
    /** @var PHPMailer $phpMailer */
    private $phpMailer;

    /** @var string $status */
    private $status;

    /** @var MailerConfigInterface $config */
    private $config;

    /**
     * @inheritDoc
     */
    public function __construct(PHPMailer $mailer, MailerConfigInterface $config, LoggerInterface $logger = null)
    {
        $this->config = $config;
        $this->phpMailer = $mailer;
        $this->phpMailer->Priority = $config->getConfigValue('priority', 3);
        $this->phpMailer->CharSet = $config->getConfigValue('charset', 'iso-8859-1');
        $this->phpMailer->ContentType = $config->getConfigValue('contenttype', 'text/plain');
        $this->phpMailer->Encoding = $config->getConfigValue('encoding', '8bit');
        $this->phpMailer->From = $config->getConfigValue('sender_email', '');
        $this->phpMailer->FromName = $config->getConfigValue('sender_name', '');
        $this->phpMailer->WordWrap = $config->getConfigValue('wordwrap', 0);
        $this->phpMailer->Mailer = $config->getConfigValue('mailer', 'mail');
        $this->phpMailer->Host = $config->getConfigValue('host', 'localhost');
        $this->phpMailer->Hostname = $config->getConfigValue('hostname', '');
        $this->phpMailer->Port = $config->getConfigValue('port', 25);
        $this->phpMailer->Helo = $config->getConfigValue('helo', '');
        $this->phpMailer->SMTPSecure = $config->getConfigValue('smtp_security', '');
        $this->phpMailer->SMTPAuth = $config->getConfigValue('smtp_enabled', false);
        $this->phpMailer->AuthType = $config->getConfigValue('auth_type', 'smtp');
        $this->phpMailer->SMTPAutoTLS = $config->getConfigValue('smtp_autotls_enabled', false);
        $this->phpMailer->SMTPOptions = $config->getConfigValue('smtp_options', []);
        $this->phpMailer->Username = $config->getConfigValue('username', '');
        $this->phpMailer->Password = $config->getConfigValue('password', '');
        $this->phpMailer->Timeout = $config->getConfigValue('timeout', 30);
        $this->phpMailer->SMTPDebug = $config->getConfigValue('smtp_debug', 0);
        $this->phpMailer->SMTPKeepAlive = $config->getConfigValue('smtp_keepalive', false);
        $this->phpMailer->SingleTo = $config->getConfigValue('singleto', false);
        $this->phpMailer->Sendmail = $config->getConfigValue('sendmail', '/usr/sbin/sendmail');
        $this->status = self::STATUS_PREPARE;
        if ($logger !== null) {
            $this->phpMailer->Debugoutput =  $logger;
        }
    }

    /**
     * @inheritDoc
     */
    public function sendEmail(EmailMessage $email):bool
    {
        try {
            $this->phpMailer->Subject = $email->getSubject();
            $this->phpMailer->Body = $email->getBody();
            if ($email->isHtml()) {
                $this->phpMailer->isHTML(true);
            }
            foreach ($email->getRecipients() as $recipient) {
                $this->phpMailer->addAddress($recipient->getEmail(), $recipient->getName());
            }
            foreach ($email->getCcRecipients() as $cc) {
                $this->phpMailer->addCC($cc->getEmail(), $cc->getName());
            }
            foreach ($email->getBccRecipients() as $bcc) {
                $this->phpMailer->addBCC($bcc->getEmail(), $bcc->getName());
            }
            foreach ($email->getAttachments() as $attachment) {

                switch (get_class($attachment)) {

                    case FileAttachment::class:
                        /** @var FileAttachment $attachment */
                        $this->phpMailer->addAttachment(
                            $attachment->getPath(),
                            $attachment->getName(),
                            $attachment->getEncoding(),
                            $attachment->getType()
                        );
                        break;

                    case ImageAttachment::class:
                        /** @var ImageAttachment $attachment */
                        $this->phpMailer->addEmbeddedImage(
                            $attachment->getPath(),
                            $attachment->getCid(),
                            $attachment->getName(),
                            $attachment->getEncoding(),
                            $attachment->getType(),
                            $attachment->getDisposition()
                        );
                        break;

                    case StringAttachment::class:
                        /** @var StringAttachment $attachment */
                        $this->phpMailer->addStringAttachment(
                            $attachment->getContent(),
                            $attachment->getName(),
                            $attachment->getEncoding(),
                            $attachment->getType(),
                            $attachment->getDisposition()
                        );
                        break;

                    default:
                        break;
                }
            }
            $success = $this->phpMailer->send();
            if ($success === true) {
                $this->status = self::STATUS_SUCCSESS;
            } else {
                $this->status = self::STATUS_FAILED;
            }
            if ($this->phpMailer->isError()) {
                $this->status = self::STATUS_ERROR;
            }

            return $success;
        } catch (Exception $e) {
            $this->status = self::STATUS_ERROR;
            throw new MailerTransportException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @inheritDoc
     */
    public function hasErrors():bool
    {
        return $this->phpMailer->isError();
    }

    /**
     * @inheritDoc
     */
    public function getStatus():string
    {
        return $this->status;
    }

    /**
     * @inheritDoc
     */
    public function getErrorMessages():array
    {
        if (!$this->hasErrors()) {
            return [];
        }

        return [$this->phpMailer->ErrorInfo];
    }

    /**
     * @inheritDoc
     */
    public function getConfigValues():array
    {
        return $this->config->getValues();
    }
}
