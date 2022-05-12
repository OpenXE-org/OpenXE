<?php

declare(strict_types=1);

namespace Xentral\Components\MailClient\Data;

use DateTime;
use DateTimeInterface;
use JsonSerializable;
use Throwable;
use Xentral\Components\MailClient\Exception\InvalidArgumentException;
use Xentral\Components\Mailer\Data\EmailRecipient;
use Xentral\Components\Util\StringUtil;

final class MailMessageData implements MailMessageInterface, JsonSerializable
{
    /** @var EmailRecipient $sender */
    private $sender;

    /** @var EmailRecipient[] $recipients */
    private $recipients;

    /** @var EmailRecipient[] $ccs */
    private $ccs;

    /** @var array $flags */
    private $flags;

    /** @var MailMessagePartInterface $contentPart */
    private $contentPart;

    /** @var string $rawContent */
    private $rawContent;

    /**
     * @param EmailRecipient $sender
     * @param array          $recipients
     * @param array          $ccs
     * @param array          $flags
     * @param array          $headers
     * @param string|null    $content
     * @param array          $parts
     * @param string|null    $rawContent
     */
    public function __construct(
        EmailRecipient $sender,
        array $recipients,
        array $ccs,
        array $flags,
        array $headers,
        ?string $content,
        array $parts = [],
        ?string $rawContent = null
    ) {
        $this->sender = $sender;
        $this->recipients = $recipients;
        $this->ccs = $ccs;
        $this->flags = $flags;
        $this->rawContent = $rawContent;
        $this->contentPart = new MailMessagePartData($headers, $content, $parts);
    }

    /**
     * @param array $data
     *
     * @throws InvalidArgumentException
     *
     * @return MailMessageData
     */
    public static function fromJsonArray(array $data): MailMessageData
    {
        if (!isset($data['sender'], $data['recipients'], $data['ccs'], $data['flags'])) {
            throw new InvalidArgumentException('Message data incomplete');
        }
        $sender = new EmailRecipient($data['sender']['email'], $data['sender']['name']);
        $recipients = [];
        foreach ($data['recipients'] as $recipientArray) {
            $recipients[] = new EmailRecipient($recipientArray['email'], $recipientArray['name']);
        }
        $ccs = [];
        foreach ($data['ccs'] as $ccArray) {
            $ccs[] = new EmailRecipient($ccArray['email'], $ccArray['name']);
        }
        $raw = isset($data['raw']) ? $data['raw'] : null;
        $contentPart = isset($data['content'])
            ? MailMessagePartData::fromJsonArray($data['content'])
            : null;
        $message =  new self(
            $sender,
            $recipients,
            $ccs,
            $data['flags'],
            [],
            null,
            [],
            $raw
        );
        $message->contentPart = $contentPart;

        return $message;
    }

    /**
     * @return array
     */
    public function getFlags(): array
    {
        return $this->flags;
    }

    /**
     * @param string $flag
     *
     * @return bool
     */
    public function hasFlag(string $flag): bool
    {
        return array_key_exists($flag, $this->flags);
    }

    /**
     * @return bool
     */
    public function isMultipart(): bool
    {
        $contentType = $this->getContentType();
        if ($contentType === null) {
            return false;
        }

        return StringUtil::startsWith($contentType, 'multipart/');
    }

    /**
     * @return string
     */
    public function getContentType(): string
    {
        return $this->contentPart->getContentType();
    }

    /**
     * @return MailAttachmentInterface[]
     */
    public function getAttachments(): array
    {
        $parts = [];
        $this->findAttachmentParts($this->contentPart, $parts);
        $attachments = [];
        foreach ($parts as $part) {
            $attachments[] = MailAttachmentData::fromMailMessagePart($part);
        }

        return $attachments;
    }

    /**
     * @param MailMessagePartInterface $part
     * @param array                    $resultArray
     *
     * @return void
     */
    private function findAttachmentParts(MailMessagePartInterface $part, array &$resultArray): void
    {
        try {
            $header = $part->getHeader('content-disposition');
            $split = explode(';', $header->getValue());
            if ($split[0] === 'attachment' || $split[0] === 'inline') {
                $resultArray[] = $part;

                return;
            }
        } catch (Throwable $e) {
            for ($i = 0; $i < $part->countParts(); $i++) {
                $this->findAttachmentParts($part->getPart($i), $resultArray);
            }
        }
    }

    /**
     * @codeCoverageIgnore
     *
     * @return MailMessageHeaderValue[]|[]
     */
    public function getHeaders(): array
    {
        return $this->contentPart->getHeaders();
    }

    /**
     * @return string|null
     */
    public function getContent(): ?string
    {
        return $this->contentPart->getContent();
    }

    /**
     * @return string|null
     */
    public function getDecodedContent(): ?string
    {
        $this->contentPart->getDecodedContent();
    }

    /**
     * @param int $index
     *
     * @codeCoverageIgnore
     *
     * @return MailMessagePartInterface
     */
    public function getPart(int $index): MailMessagePartInterface
    {
        return $this->contentPart->getPart($index);
    }

    /**
     * @codeCoverageIgnore
     *
     * @return int
     */
    public function countParts(): int
    {
        return $this->contentPart->countParts();
    }

    /**
     * @return string|null
     */
    public function getHtmlBody(): ?string
    {
        $part = $this->findPartByContentType($this->contentPart, 'text/html');
        if ($part === null) {
            return null;
        }

        return $part->getDecodedContent();
    }

    /**
     * @param MailMessagePartInterface $part
     * @param string                   $contentType
     *
     * @return MailMessagePartInterface|null
     */
    private function findPartByContentType(
        MailMessagePartInterface $part,
        string $contentType
    ): ?MailMessagePartInterface {
        if ($part->getContentType() === $contentType) {
            return $part;
        }
        for ($i = 0; $i < $part->countParts(); $i++) {
            $subPart = $this->findPartByContentType($part->getPart($i), $contentType);
            if ($subPart !== null) {
                return $subPart;
            }
        }

        return null;
    }

    /**
     * @return string|null
     */
    public function getPlainTextBody(): ?string
    {
        $part = $this->findPartByContentType($this->contentPart, 'text/plain');
        if ($part === null) {
            return null;
        }

        return $part->getDecodedContent();
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        $subject = $this->getHeader('subject');
        if ($subject === null) {
            return '';
        }

        return $subject->getValue();
    }

    /**
     * @param string $name
     *
     * @codeCoverageIgnore
     *
     * @return MailMessageHeaderValue|null
     */
    public function getHeader(string $name): ?MailMessageHeaderValue
    {
        return $this->contentPart->getHeader($name);
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getDate(): ?DateTimeInterface
    {
        $date = $this->getHeader('date');
        if ($date === null) {
            return null;
        }
        $dateTime = DateTime::createFromFormat(DateTimeInterface::RFC2822, $date->getValue());
        if ($dateTime === false) {
            $dateTime = DateTime::createFromFormat(DateTimeInterface::RFC822, $date->getValue());
        }
        if ($dateTime === false) {
            return null;
        }

        return $dateTime;
    }

    /**
     * @return EmailRecipient
     */
    public function getSender(): EmailRecipient
    {
        return $this->sender;
    }

    /**
     * @return string
     */
    public function getReplyToAddress(): string
    {
        $header = $this->getHeader('return-path');
        if ($header === null) {
            return $this->sender->getEmail();
        }
        $address = $header->getValue();
        if (preg_match('/(.*)<(.+@.+)>/', $address, $matches)) {
            $address = $matches[2];
        }

        return $address;
    }

    /**
     * @return EmailRecipient[]
     */
    public function getRecipients(): array
    {
        return $this->recipients;
    }

    /**
     * @return EmailRecipient[]
     */
    public function getCcRecipients(): array
    {
        return $this->ccs;
    }

    /**
     * @return string|null
     */
    public function getRawContent(): ?string
    {
        if ($this->rawContent === null) {
            return '';
        }

        return $this->rawContent;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'sender'     => $this->sender,
            'recipients' => $this->recipients,
            'ccs'        => $this->ccs,
            'flags'      => $this->flags,
            'content'    => $this->contentPart,
            'raw'        => $this->rawContent,
        ];
    }
}
