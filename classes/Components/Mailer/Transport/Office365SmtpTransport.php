<?php

declare(strict_types=1);

namespace Xentral\Components\Mailer\Transport;

use Psr\Log\LoggerInterface;
use Xentral\Components\Mailer\Config\MailerConfigInterface;
use Xentral\Components\Mailer\Data\EmailMessage;
use Xentral\Components\Mailer\Data\FileAttachment;
use Xentral\Components\Mailer\Data\ImageAttachment;
use Xentral\Components\Mailer\Data\StringAttachment;
use Xentral\Components\Mailer\Exception\Office365SmtpException;
use Xentral\Modules\Office365Api\Data\Office365AccountData;
use Xentral\Modules\Office365Api\Service\Office365AccountGateway;
use Xentral\Modules\Office365Api\Service\Office365AuthorizationService;

final class Office365SmtpTransport implements MailerTransportInterface
{
    private $config;
    private $status;
    private $errors = [];
    private $logger;
    private $socket;
    private $senderEmail;
    private $gateway;
    private $authorizationService;
    private $office365Account;

    public function __construct(
        MailerConfigInterface $config,
        Office365AccountGateway $gateway,
        Office365AuthorizationService $authorizationService,
        Office365AccountData $office365Account,
        ?LoggerInterface $logger = null
    ) {
        $this->config = $config;
        $this->gateway = $gateway;
        $this->authorizationService = $authorizationService;
        $this->office365Account = $office365Account;
        $this->logger = $logger;
        $this->status = self::STATUS_PREPARE;
        $this->socket = null;
    }

    public function sendEmail(EmailMessage $email): bool
    {
        try {
            $senderEmail = $this->config->getConfigValue('sender_email', '');
            if (empty($senderEmail)) {
                throw new Office365SmtpException('Sender email not configured');
            }
            $this->senderEmail = $senderEmail;

            $token = $this->getOAuth2Token();
            if (empty($token)) {
                throw new Office365SmtpException('Failed to obtain OAuth2 token');
            }

            $host = $this->config->getConfigValue('host', 'smtp.office365.com');
            $port = $this->config->getConfigValue('port', 587);

            $this->connect($host, $port);
            $this->starttls();
            $this->authenticate($token, $senderEmail);
            $this->sendMessage($email);
            $this->disconnect();

            $this->status = self::STATUS_SUCCSESS;
            return true;
        } catch (Office365SmtpException $e) {
            $this->errors[] = $e->getMessage();
            $this->status = self::STATUS_ERROR;
            $this->safeDisconnect();
            if ($this->logger) {
                $this->logger->error('Office365SmtpTransport: ' . $e->getMessage());
            }
            return false;
        } catch (\Exception $e) {
            $this->errors[] = $e->getMessage();
            $this->status = self::STATUS_ERROR;
            $this->safeDisconnect();
            if ($this->logger) {
                $this->logger->error('Office365SmtpTransport: Unexpected error: ' . $e->getMessage());
            }
            return false;
        }
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    public function getErrorMessages(): array
    {
        return $this->errors;
    }

    public function getConfigValues(): array
    {
        return $this->config->getValues();
    }

    private function connect(string $host, int $port): void
    {
        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => true,
                'verify_peer_name' => true,
                'allow_self_signed' => false
            ]
        ]);

        $this->socket = @stream_socket_client(
            "tcp://{$host}:{$port}",
            $errno,
            $errstr,
            30,
            STREAM_CLIENT_CONNECT,
            $context
        );

        if (!$this->socket) {
            throw new Office365SmtpException("Failed to connect to {$host}:{$port}: {$errstr}");
        }

        stream_set_timeout($this->socket, 30);

        $response = $this->readResponse();
        if (strpos($response, '220') === false) {
            throw new Office365SmtpException("Invalid server response: {$response}");
        }

        $this->sendCommand('EHLO ' . php_uname('n'));
    }

    private function starttls(): void
    {
        $this->sendCommand('STARTTLS');

        if (!stream_socket_enable_crypto($this->socket, true, STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT)) {
            throw new Office365SmtpException('Failed to establish TLS connection');
        }

        $this->sendCommand('EHLO ' . php_uname('n'));
    }

    private function authenticate(string $token, string $email): void
    {
        $authString = "user={$email}\001auth=Bearer {$token}\001\001";
        $encodedAuth = base64_encode($authString);

        $response = $this->sendCommand('AUTH XOAUTH2');

        if (strpos($response, '334') === false) {
            throw new Office365SmtpException('Server did not accept AUTH XOAUTH2: ' . $response);
        }

        $response = $this->sendCommand($encodedAuth);

        if (strpos($response, '235') === false) {
            throw new Office365SmtpException('Authentication failed: ' . $response);
        }
    }

    private function sendMessage(EmailMessage $email): void
    {
        if (empty($email->getRecipients())) {
            throw new Office365SmtpException('No recipients specified');
        }

        $this->sendCommand('MAIL FROM:<' . $this->senderEmail . '>');

        foreach ($email->getRecipients() as $recipient) {
            $this->sendCommand('RCPT TO:<' . $recipient->getEmail() . '>');
        }

        foreach ($email->getCcRecipients() as $cc) {
            $this->sendCommand('RCPT TO:<' . $cc->getEmail() . '>');
        }

        foreach ($email->getBccRecipients() as $bcc) {
            $this->sendCommand('RCPT TO:<' . $bcc->getEmail() . '>');
        }

        $this->sendCommand('DATA');

        $mimeMessage = $this->buildMimeMessage($email);
        fwrite($this->socket, $mimeMessage . "\r\n.\r\n");

        $response = $this->readResponse();
        if (strpos($response, '250') === false) {
            throw new Office365SmtpException('Failed to send message: ' . $response);
        }
    }

    private function buildMimeMessage(EmailMessage $email): string
    {
        $messageId = '<' . time() . '.' . uniqid() . '@' . php_uname('n') . '>';
        $boundary = 'boundary_' . uniqid();

        $headers = [];
        $headers[] = 'From: ' . $this->getSenderHeader();
        $headers[] = 'To: ' . $this->getRecipientsHeader($email->getRecipients());

        if (!empty($email->getCcRecipients())) {
            $headers[] = 'Cc: ' . $this->getRecipientsHeader($email->getCcRecipients());
        }

        $headers[] = 'Subject: ' . $this->encodeSubject($email->getSubject());
        $headers[] = 'Date: ' . date('r');
        $headers[] = 'Message-ID: ' . $messageId;
        $headers[] = 'MIME-Version: 1.0';

        $body = '';

        if (empty($email->getAttachments())) {
            $contentType = $email->isHtml() ? 'text/html' : 'text/plain';
            $headers[] = 'Content-Type: ' . $contentType . '; charset=utf-8';
            $headers[] = 'Content-Transfer-Encoding: 8bit';
            $body = $email->getBody();
        } else {
            $headers[] = 'Content-Type: multipart/mixed; boundary="' . $boundary . '"';

            $body = '--' . $boundary . "\r\n";
            $contentType = $email->isHtml() ? 'text/html' : 'text/plain';
            $body .= 'Content-Type: ' . $contentType . '; charset=utf-8' . "\r\n";
            $body .= 'Content-Transfer-Encoding: 8bit' . "\r\n\r\n";
            $body .= $email->getBody() . "\r\n";

            foreach ($email->getAttachments() as $attachment) {
                $body .= '--' . $boundary . "\r\n";
                $body .= $this->buildAttachmentPart($attachment);
            }

            $body .= '--' . $boundary . '--' . "\r\n";
        }

        foreach ($email->getCustomHeaders() as $name => $value) {
            $headers[] = $name . ': ' . $value;
        }

        return implode("\r\n", $headers) . "\r\n\r\n" . $body;
    }

    private function buildAttachmentPart($attachment): string
    {
        $filename = '';
        $content = '';

        if ($attachment instanceof FileAttachment) {
            $filename = $attachment->getName();
            $content = file_get_contents($attachment->getPath());
            $type = $attachment->getType();
        } elseif ($attachment instanceof ImageAttachment) {
            $filename = $attachment->getName();
            $content = file_get_contents($attachment->getPath());
            $type = $attachment->getType();
        } elseif ($attachment instanceof StringAttachment) {
            $filename = $attachment->getName();
            $content = $attachment->getContent();
            $type = $attachment->getType();
        } else {
            return '';
        }

        $encoded = base64_encode($content);
        $encoded = chunk_split($encoded, 76, "\r\n");

        $part = 'Content-Type: ' . $type . '; name="' . $filename . '"' . "\r\n";
        $part .= 'Content-Transfer-Encoding: base64' . "\r\n";
        $part .= 'Content-Disposition: attachment; filename="' . $filename . '"' . "\r\n\r\n";
        $part .= $encoded . "\r\n";

        return $part;
    }

    private function getSenderHeader(): string
    {
        $senderName = $this->config->getConfigValue('sender_name', '');
        if (!empty($senderName)) {
            return '"' . str_replace('"', '\"', $senderName) . '" <' . $this->senderEmail . '>';
        }
        return $this->senderEmail;
    }

    private function getRecipientsHeader(array $recipients): string
    {
        $formatted = [];
        foreach ($recipients as $recipient) {
            $name = $recipient->getName();
            if (!empty($name)) {
                $formatted[] = '"' . str_replace('"', '\"', $name) . '" <' . $recipient->getEmail() . '>';
            } else {
                $formatted[] = $recipient->getEmail();
            }
        }
        return implode(', ', $formatted);
    }

    private function encodeSubject(string $subject): string
    {
        if (empty($subject)) {
            return '';
        }

        if (preg_match('/[^\x20-\x7E]/', $subject)) {
            return '=?UTF-8?B?' . base64_encode($subject) . '?=';
        }

        return $subject;
    }

    private function sendCommand(string $command): string
    {
        if ($this->logger) {
            $this->logger->debug('Office365SmtpTransport: Sending: ' . substr($command, 0, 50));
        }

        fwrite($this->socket, $command . "\r\n");
        return $this->readResponse();
    }

    private function readResponse(): string
    {
        $response = '';

        while (!feof($this->socket)) {
            $line = fgets($this->socket, 512);
            if ($line === false) {
                break;
            }

            $response .= $line;

            if (strpos($line, ' ') !== false && strpos($line, ' ') < 4) {
                break;
            }
        }

        if ($this->logger) {
            $this->logger->debug('Office365SmtpTransport: Response: ' . trim($response));
        }

        return $response;
    }

    private function disconnect(): void
    {
        if ($this->socket) {
            fwrite($this->socket, "QUIT\r\n");
            fclose($this->socket);
            $this->socket = null;
        }
    }

    private function safeDisconnect(): void
    {
        if ($this->socket) {
            @fclose($this->socket);
            $this->socket = null;
        }
    }

    private function getOAuth2Token(): string
    {
        $accountId = $this->office365Account->getId();

        $token = $this->gateway->getAccessToken($accountId);

        if ($token === null) {
            throw new Office365SmtpException('No access token available for Office365 account');
        }

        if ($token->getTimeToLive() < 30) {
            if ($this->logger) {
                $this->logger->info('Office365SmtpTransport: Token expired, refreshing...');
            }
            $token = $this->authorizationService->refreshAccessToken($this->office365Account);
        }

        return $token->getToken();
    }
}
