<?php

declare(strict_types=1);

namespace Xentral\Components\MailClient\Client;

use Exception;
use Laminas\Mail\Header\Cc;
use Laminas\Mail\Header\From;
use Laminas\Mail\Header\To;
use Laminas\Mail\Headers;
use Laminas\Mail\Protocol\Imap as Protocol;
use Laminas\Mail\Storage\Imap as ImapClient;
use Laminas\Mail\Storage\Message;
use Laminas\Mail\Storage\Part;
use Xentral\Components\MailClient\Config\ImapMailClientConfig;
use Xentral\Components\MailClient\Config\ImapMailClientConfigInterface;
use Xentral\Components\MailClient\Data\MailBoxInfoData;
use Xentral\Components\MailClient\Data\MailMessageData;
use Xentral\Components\MailClient\Data\MailMessageHeaderValue;
use Xentral\Components\MailClient\Data\MailMessageInterface;
use Xentral\Components\MailClient\Data\MailMessagePartData;
use Xentral\Components\MailClient\Data\MailMessagePartInterface;
use Xentral\Components\MailClient\Exception\ClientConnectionException;
use Xentral\Components\MailClient\Exception\FolderNotFoundException;
use Xentral\Components\MailClient\Exception\InvalidArgumentException;
use Xentral\Components\MailClient\Exception\LoginException;
use Xentral\Components\MailClient\Exception\MessageNotFoundException;
use Xentral\Components\MailClient\Exception\OAuthException;
use Xentral\Components\MailClient\Exception\ProtocolException;
use Xentral\Components\Mailer\Data\EmailRecipient;

final class ImapMailClient implements MailClientInterface
{
    /** @var ImapMailClientConfigInterface $config */
    private $config;

    /** @var Protocol $protocol */
    private $protocol;

    /** @var ImapClient $imap */
    private $imap;

    /**
     * @param ImapMailClientConfigInterface $config
     */
    public function __construct(ImapMailClientConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     * @throws LoginException
     *
     * @return void
     */
    public function connect(): void
    {
        $ssl = '';
        if ($this->config->isSslEnabled()) {
            $ssl = 'ssl';
        }
        $this->protocol = new Protocol(
            $this->config->getServer(),
            $this->config->getPort(),
            $ssl
        );
        switch (strtolower($this->config->getAuthType())) {
            case ImapMailClientConfig::AUTH_BASIC:
                $this->protocol->login($this->config->getUser(), $this->config->getPassword());
                break;

            case ImapMailClientConfig::AUTH_XOAUTH2:
                $this->loginOauth();
                break;

            default:
                throw new LoginException(
                    sprintf('Authentication method "%s" not supported', $this->config->getAuthType())
                );
        }
        $this->imap = new ImapClient($this->protocol);
    }

    /**
     * @return void
     */
    public function disconnect(): void
    {
        if ($this->protocol === null) {
            return;
        }
        $this->protocol->logout();
    }

    /**
     * @param string $criteria
     *
     * @throws InvalidArgumentException
     * @throws ClientConnectionException
     *
     * @return array
     */
    public function searchMessages(string $criteria): array
    {
        $this->ensureConnection();
        $criteriaArray = preg_split('/\s/', $criteria);
        $result = $this->protocol->search($criteriaArray);
        if ($result === null) {
            throw new InvalidArgumentException(sprintf('Invalid search criteria "%s".', $criteria));
        }

        return $result;
    }

    /**
     * @param int $msgNumber
     *
     * @throws MessageNotFoundException
     * @throws ClientConnectionException
     *
     * @return MailMessageInterface
     */
    public function fetchMessage(int $msgNumber): MailMessageInterface
    {
        $this->ensureConnection();
        try {
            $message = $this->imap->getMessage($msgNumber);
        } catch (Exception $e) {
            throw new MessageNotFoundException(
                sprintf('Message number %s not found.', $msgNumber)
            );
        }

        return $this->parseMessage($message);
    }

    /**
     * @param int    $msgNumber
     * @param string $targetFolder
     *
     * @throws ClientConnectionException
     *
     * @return void
     */
    public function copyMessage(int $msgNumber, string $targetFolder): void
    {
        $this->ensureConnection();
        try {
            $this->imap->copyMessage($msgNumber, $targetFolder);
        } catch (Exception $e) {
            throw new ProtocolException(
                sprintf('Failed to copy Message "%s" to Folder "%s"', $msgNumber, $targetFolder),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * @param int $msgNumber
     *
     * @throws ProtocolException
     * @throws ClientConnectionException
     *
     * @return void
     */
    public function deleteMessage(int $msgNumber): void
    {
        $this->ensureConnection();
        try {
            $this->imap->removeMessage($msgNumber);
        } catch (Exception $e) {
            throw new ProtocolException('Failed do delete Message.', $e->getCode(), $e);
        }
    }

    /**
     * @param string $inbox
     *
     * @throws FolderNotFoundException
     * @throws ClientConnectionException
     *
     * @return MailBoxInfoData
     */
    public function examineInbox(string $inbox = null): MailBoxInfoData
    {
        $this->ensureConnection();
        if ($inbox === null) {
            $inbox = $this->config->getInboxFolder();
        }
        $status = $this->protocol->examine($inbox);
        if ($status === false) {
            throw new FolderNotFoundException(
                sprintf('Cannot examine "%s" - folder probably not existing.', $inbox)
            );
        }

        return new MailBoxInfoData(
            (int)$status['exists'],
            (int)$status['recent'],
            (int)$status['uidvalidity'],
            $status['flags'][0]
        );
    }

    /**
     * @throws ClientConnectionException
     *
     * @return bool
     */
    public function expunge(): bool
    {
        $this->ensureConnection();
        $result = $this->protocol->expunge();

        return $result === true;
    }

    /**
     * Sets Flags on message.
     *
     * @param int      $msgNumber
     * @param string[] $flags values: '\Seen' '\Answered' '\Flagged' '\Deleted' '\Draft'
     *
     * @throws ProtocolException
     * @throws ClientConnectionException
     *
     * @return void
     */
    public function setFlags(int $msgNumber, array $flags): void
    {
        $this->ensureConnection();
        try {
            $this->imap->setFlags($msgNumber, $flags);
        } catch (Exception $e) {
            throw new ProtocolException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param string $folder
     *
     * @throws FolderNotFoundException
     * @throws ClientConnectionException
     *
     * @return void
     */
    public function selectFolder(string $folder): void
    {
        $this->ensureConnection();
        try {
            $this->imap->selectFolder($folder);
        } catch (Exception $e) {
            throw new FolderNotFoundException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @throws ProtocolException
     * @throws ClientConnectionException
     *
     * @return void
     */
    public function noop(): void
    {
        $this->ensureConnection();
        try {
            $this->imap->noop();
        } catch (Exception $e) {
            throw new ProtocolException('NOOP Command Failed');
        }
    }

    /**
     * @param string $message
     * @param string $targetFolder
     *
     * @throws ProtocolException
     *
     * @return void
     */
    public function appendMessage(string $message, string $targetFolder): void
    {
        try {
            $this->imap->appendMessage($message, $targetFolder);
        } catch (Exception $e) {
            throw new ProtocolException('Failed to append message.', $e->getCode(), $e);
        }
    }

    /**
     * @throws OAuthException
     *
     * @return void
     */
    private function loginOauth(): void
    {
        $authString = sprintf(
            "user=%s\1auth=Bearer %s\1\1",
            $this->config->getUser(),
            $this->config->getPassword()
        );
        $authString = base64_encode($authString);
        $this->protocol->sendRequest('AUTHENTICATE', ['XOAUTH2', $authString]);

        while (true) {
            $response = '';
            $isPlus = $this->protocol->readLine($response, '+', true);
            if ($isPlus) {
                $this->protocol->sendRequest('');
                continue;
            }
            if (preg_match("/^OK /i", $response)) {
                return;
            }
            if (preg_match('/^NO (.+)/i', $response, $matches)) {
                throw new LoginException(
                    sprintf('OAuth access denied: %s', $matches[1])
                );
            }
            if (preg_match('/^BAD (.+)/i', $response, $matches)) {
                throw new LoginException(
                    sprintf('OAuth login error: %s', $matches[1])
                );
            }
        }
    }

    /**
     * @param Message $message
     *
     * @return MailMessageData
     */
    private function parseMessage(Message $message): MailMessageData
    {
        /** @var From $from */
        $from = $message->getHeader('From');
        $list = $from->getAddressList();
        $sender = new EmailRecipient($list->current()->getEmail(), $list->current()->getName());
        try {
            $recipients = [];
            /** @var To $toHeader */
            $toHeader = $message->getHeader('To');
            foreach ($toHeader->getAddressList() as $recipient) {
                $recipients[] = new EmailRecipient($recipient->getEmail(), $recipient->getName());
            }
        } catch (Exception $e) {
            $recipients = [];
        }
        try {
            /** @var Cc $ccHeader */
            $ccHeader = $message->getHeader('Cc');
            $ccs = [];
            foreach ($ccHeader->getAddressList() as $cc) {
                $ccs[] = new EmailRecipient($cc->getEmail(), $cc->getName());
            }
        } catch (Exception $e) {
            $ccs = [];
        }
        $raw = $message->getContent();
        $content = null;
        $parts = $this->parseMessagePartsRecursive($message);
        if (count($parts) === 0) {
            $content = $raw;
        }

        return new MailMessageData(
            $sender,
            $recipients,
            $ccs,
            $message->getFlags(),
            $this->parseHeaders($message->getHeaders()),
            $content,
            $this->parseMessagePartsRecursive($message),
            $raw
        );
    }

    /**
     * @param Headers $headers
     *
     * @return array
     */
    private function parseHeaders(?Headers $headers): array
    {
        if ($headers === null) {
            return [];
        }
        $headerArray = [];
        foreach ($headers as $header) {
            $key = strtolower($header->getFieldName());
            $val = new MailMessageHeaderValue(
                $header->getFieldName(),
                $header->getFieldValue(),
                $header->getEncoding()
            );
            $headerArray[$key] = $val;
        }

        return $headerArray;
    }

    /**
     * @param Part $message
     *
     * @return MailMessagePartInterface[]
     */
    private function parseMessagePartsRecursive(Part $message): array
    {
        if ($message->countParts() === 0) {
            return [];
        }
        $parts = [];
        $partsCount = (int)$message->countParts();
        for ($i = 1; $i <= $partsCount; $i++) {
            $part = $message->getPart($i);
            $headers = $this->parseHeaders($part->getHeaders());
            $content = null;
            $subParts = $this->parseMessagePartsRecursive($part);
            if (count($subParts) === 0) {
                $content = $part->getContent();
            }

            $parts[] = new MailMessagePartData(
                $headers,
                $content,
                $subParts
            );
        }

        return $parts;
    }

    /**
     * @throws ClientConnectionException
     *
     * @return void
     */
    private function ensureConnection(): void
    {
        if ($this->protocol === null || $this->imap === null) {
            throw new ClientConnectionException('IMAP client not connected.');
        }
    }
}
