<?php

declare(strict_types=1);

namespace Xentral\Modules\SystemMailer\Data;

use DateTime;
use DateTimeInterface;

final class EmailBackupAccount implements MailAccountInterface
{
    /** @var string AUTH_STANDARD */
    public const AUTH_STANDARD = '';

    /** @var string AUTH_SMTP */
    public const AUTH_SMTP = 'smtp';

    /** @var string AUTH_GMAIL */
    public const AUTH_GMAIL = 'oauth_google';

    /** @var int $id */
    private $id;

    /** @var string $displayName */
    private $displayName;

    /** @var string $description */
    private $description;

    /** @var string $username */
    private $username;

    /** @var string $password */
    private $password;

    /** @var string $server */
    private $server;

    /** @var string $emailAddress */
    private $emailAddress;

    /** @var bool $imapIsSentFolderActive */
    private $imapIsSentFolderActive;

    /** @var string $imapSentFolder */
    private $imapSentFolder;

    /** @var int $imapPort */
    private $imapPort;

    /** @var int $imapType */
    private $imapType;

    /** @var int $letterTemplateId */
    private $letterTemplateId;

    /** @var bool $isAutoresponseActive */
    private $isAutoresponseActive;

    /** @var string$autoresponseSubject */
    private $autoresponseSubject;

    /** @var string $autoresponseText */
    private $autoresponseText;

    /** @var bool $autoResponseLimitEnabled */
    private $autoResponseLimitEnabled;

    /** @var int $projectId */
    private $projectId;

    /** @var bool $isBackupEnabled */
    private $isBackupEnabled;

    /** @var int $backupDeleteAfter */
    private $backupDeleteAfter;

    /** @var int $companyId */
    private $companyId;

    /** @var int $addressId */
    private $addressId;

    /** @var bool $isTicketSystemActive */
    private $isTicketSystemActive;

    /** @var bool $isDeleted */
    private $isDeleted;

    /** @var bool $isTicketDeleteEnabled */
    private $isTicketDeleteEnabled;

    /** @var bool $isTicketMarkAsFinishedEnabled */
    private $isTicketMarkAsFinishedEnabled;

    /** @var string $ticketQueueId */
    private $ticketQueueId;

    /** @var int $ticketProjectId */
    private $ticketProjectId;

    /** @var bool$isTicketInboundEmailAddressEnabled */
    private $isTicketInboundEmailAddressEnabled;

    /** @var string $ticketStartDay */
    private $ticketStartDay;

    /** @var bool $isSmtpActive */
    private $isSmtpActive;

    /** @var string $smtpServer */
    private $smtpServer;

    /** @var string $smtpSecurity */
    private $smtpSecurity;

    /** @var int $smtpPort */
    private $smtpPort;

    /** @var string $smtpSenderEmail */
    private $smtpSenderEmail;

    /** @var string $smtpSenderName */
    private $smtpSenderName;

    /** @var bool $isSignatureActive */
    private $isSignatureActive;

    /** @var string $signatureText */
    private $signatureText;

    /** @var int $signatureMutex */
    private $signatureMutex;

    /** @var string $smtpAuthType */
    private $smtpAuthType;

    /** @var string $smtpAuthParam */
    private $smtpAuthParam;

    /** @var bool $smtpDebugEnabled */
    private $smtpDebugEnabled;

    /** @var string $clientAlias */
    private $clientAlias;

    /**
     * @param array $data
     *
     * @return EmailBackupAccount
     */
    public static function fromDbState(array $data):EmailBackupAccount
    {
        $instance = new self();
        $instance->id = (int)($data['id'] ?? 0);
        $instance->displayName = $data['angezeigtername'] ?? '';
        $instance->emailAddress = $data['email'] ?? '';
        $instance->description = $data['internebeschreibung'] ?? '';
        $instance->username = $data['benutzername'] ?? '';
        $instance->password = $data['passwort'] ?? '';
        $instance->server = $data['server'] ?? '';
        $instance->imapIsSentFolderActive = 1 === (int)($data['imap_sentfolder_aktiv'] ?? 0);
        $instance->imapSentFolder = $data['imap_sentfolder'] ?? '';
        $instance->imapPort = (int)($data['imap_port'] ?? 0);
        $instance->imapType = (int)($data['imap_type'] ?? 0);
        $instance->letterTemplateId = (int)($data['geschaeftsbriefvorlage'] ?? 0);
        $instance->isAutoresponseActive = 1 === (int)($data['autoresponder'] ?? 0);
        $instance->autoresponseSubject = $data['autoresponderbetreff'] ?? '';
        $instance->autoresponseText = $data['autorespondertext'] ?? '';
        $instance->autoResponseLimitEnabled = 1 === (int)($data['autosresponder_blacklist'] ?? null);
        if (array_key_exists('autoresponder_blacklist', $data)) {
            $instance->autoResponseLimitEnabled = 1 === (int)($data['autoresponder_blacklist'] ?? 0);
        }
        $instance->projectId = (int)($data['projekt'] ?? 0);
        $instance->isBackupEnabled = 1 === (int)($data['emailbackup'] ?? 0);
        $instance->backupDeleteAfter = (int)($data['loeschtage'] ?? 0);
        $instance->addressId = (int)($data['adresse'] ?? 0);
        $instance->companyId = (int)($data['firma'] ?? 0);
        $instance->isDeleted = 1 === (int)($data['geloescht'] ?? 0);
        $instance->isTicketSystemActive = 1 === (int)($data['ticket'] ?? 0);
        $instance->isTicketDeleteEnabled = 1 === (int)($data['ticketloeschen'] ?? 0);
        $instance->isTicketMarkAsFinishedEnabled = 1 === (int)($data['ticketabgeschlossen'] ?? 0);
        $instance->ticketQueueId = $data['ticketqueue'] ?? '';
        $instance->ticketProjectId = (int)($data['ticketprojekt'] ?? 0);
        $instance->isTicketInboundEmailAddressEnabled = 1 === (int)($data['ticketemaileingehend'] ?? 0);
        $instance->ticketStartDay = $data['abdatum'] ?? '';
        $instance->isSmtpActive = 1 === (int)($data['smtp_extra'] ?? 0);
        $instance->smtpServer = $data['smtp'] ?? '';
        switch ((int)($data['smtp_ssl'] ?? 0)) {
            case 0:
                $instance->smtpSecurity = '';
                break;

            case 1:
                $instance->smtpSecurity = 'tls';
                break;

            case 2:
                $instance->smtpSecurity = 'ssl';
                break;

            default:
                $instance->smtpSecurity = '';
        }
        $instance->smtpPort = (int)($data['smtp_port'] ?? 0);
        $instance->smtpSenderEmail = $data['smtp_frommail'] ?? '';
        $instance->smtpSenderName = $data['smtp_fromname'] ?? '';
        $instance->smtpAuthType = mb_strtolower($data['smtp_authtype'] ?? '');
        $instance->smtpAuthParam = $data['smtp_authparam'] ?? '';
        $instance->smtpDebugEnabled = (int)($data['smtp_loglevel'] ?? 0) > 0;
        $instance->clientAlias = $data['client_alias'] ?? '';
        $instance->isSignatureActive =  1 === (int)($data['eigenesignatur'] ?? 0);
        $instance->signatureText = $data['signatur'] ?? '';
        $instance->signatureMutex = (int)($data['mutex'] ?? 0);

        return $instance;
    }

    /**
     * @param string $name
     *
     * @return EmailBackupAccount
     */
    public function withSmtpSenderName(string $name): EmailBackupAccount
    {
        $clone = clone($this);
        $clone->smtpSenderName = $name;

        return $clone;
    }

    /**
     * @param string $name
     *
     * @return EmailBackupAccount
     */
    public function withDisplayName(string $name): EmailBackupAccount
    {
        $clone = clone($this);
        $clone->displayName = $name;

        return $clone;
    }

    /**
     * @inheritDoc
     */
    public function getType():string
    {
        if (!$this->isSmtpActive) {
            return 'imap';
        }

        return $this->smtpAuthType;
    }

    /**
     * @inheritDoc
     */
    public function getServerAddress():string
    {
        if ($this->isSmtpActive === true) {
            return $this->smtpServer;
        }

        return $this->server;
    }

    /**
     * @inheritDoc
     */
    public function getClientAlias():string
    {
        return $this->clientAlias;
    }

    /**
     * @inheritDoc
     */
    public function getSenderEmailAddress():string
    {
        if ($this->isSmtpActive === true && $this->smtpSenderEmail !== '') {
            return $this->smtpSenderEmail;
        }

        return $this->emailAddress;
    }

    /**
     * @inheritDoc
     */
    public function getSenderName():string
    {
        if ($this->isSmtpActive === true && $this->smtpSenderEmail !== '') {
            return $this->smtpSenderEmail;
        }

        return $this->displayName;
    }

    /**
     * @inheritDoc
     */
    public function getUserName():string
    {
        if ($this->smtpAuthType === MailAccountInterface::TYPE_GOOGLE) {
            return $this->smtpSenderEmail;
        }

        return $this->username;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getImapServer(): string
    {
        return $this->server;
    }

    /**
     * @return string
     */
    public function getEmailAddress(): string
    {
        return $this->emailAddress;
    }

    /**
     * @return bool
     */
    public function isCopyToOutgoingFolderEnabled(): bool
    {
        return $this->imapIsSentFolderActive && !empty($this->imapSentFolder);
    }

    /**
     * @return string
     */
    public function getImapOutgoingFolder(): string
    {
        return $this->imapSentFolder;
    }

    /**
     * @return int
     */
    public function getImapPort(): int
    {
        return $this->imapPort;
    }

    /**
     * @return int
     */
    public function getImapType(): int
    {
        return $this->imapType;
    }

    /**
     * @return int
     */
    public function getLetterTemplateId(): int
    {
        return $this->letterTemplateId;
    }

    /**
     * @return bool
     */
    public function isAutoresponseEnabled(): bool
    {
        return $this->isAutoresponseActive;
    }

    /**
     * @return string
     */
    public function getAutoresponseSubject(): string
    {
        return $this->autoresponseSubject;
    }

    /**
     * @return string
     */
    public function getAutoresponseText(): string
    {
        return $this->autoresponseText;
    }

    /**
     * @return bool
     */
    public function isAutoresponseLimitEnabled(): bool
    {
        return $this->autoResponseLimitEnabled;
    }

    /**
     * @return int
     */
    public function getProjectId(): int
    {
        return $this->projectId;
    }

    /**
     * @return bool
     */
    public function isBackupEnabled(): bool
    {
        return $this->isBackupEnabled;
    }

    /**
     * @return int
     */
    public function getBackupDeleteAfterDays(): int
    {
        return $this->backupDeleteAfter;
    }

    /**
     * @return int
     */
    public function getCompanyId(): int
    {
        return $this->companyId;
    }

    /**
     * @return int
     */
    public function getAddressId(): int
    {
        return $this->addressId;
    }

    /**
     * @return bool
     */
    public function isTicketImportEnabled(): bool
    {
        return $this->isTicketSystemActive;
    }

    /**
     * @return bool
     */
    public function isMarkedAsDeleted(): bool
    {
        return $this->isDeleted;
    }

    /**
     * @return bool
     */
    public function isDeleteAfterImportEnabled(): bool
    {
        return $this->isTicketDeleteEnabled;
    }

    /**
     * @return bool
     */
    public function isTicketMarkAsFinishedEnabled(): bool
    {
        return $this->isTicketMarkAsFinishedEnabled;
    }

    /**
     * @return string
     */
    public function getTicketQueueId(): string
    {
        return $this->ticketQueueId;
    }

    /**
     * @return int
     */
    public function getTicketProjectId(): int
    {
        return $this->ticketProjectId ?? 0;
    }

    /**
     * @return bool
     */
    public function isTicketInboundEmailAddressEnabled(): bool
    {
        return $this->isTicketInboundEmailAddressEnabled ?? false;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getImportStartDate(): ?DateTimeInterface
    {
        if (empty($this->ticketStartDay) || $this->ticketStartDay === '0000-00-00') {
            return null;
        }
        $dateTime = DateTime::createFromFormat('Y-m-d', (string)$this->ticketStartDay);
        if ($dateTime === false) {
            return null;
        }

        return $dateTime;
    }

    /**
     * @return string
     */
    public function getImportStartDateAsString(): string
    {
        // ensure legacy code compatibility
        if (!is_string($this->ticketStartDay) || empty($this->ticketStartDay)) {
            return '0000-00-00';
        }

        return $this->ticketStartDay;
    }

    /**
     * @return bool
     */
    public function isSmtpEnabled(): bool
    {
        return $this->isSmtpActive;
    }

    /**
     * @return string
     */
    public function getSmtpServer(): string
    {
        return $this->smtpServer;
    }

    /**
     * @return string
     */
    public function getSmtpSecurity(): string
    {
        return $this->smtpSecurity;
    }

    /**
     * @return int
     */
    public function getSmtpPort(): int
    {
        return $this->smtpPort;
    }

    /**
     * @return string
     */
    public function getSmtpSenderEmail(): string
    {
        return $this->smtpSenderEmail;
    }

    /**
     * @return string
     */
    public function getSmtpSenderName(): string
    {
        return $this->smtpSenderName;
    }

    /**
     * @return bool
     */
    public function isSignatureEnabled(): bool
    {
        return $this->isSignatureActive;
    }

    /**
     * @return string
     */
    public function getSignatureText(): string
    {
        return $this->signatureText;
    }

    /**
     * @return string
     */
    public function getSmtpAuthType(): string
    {
        return $this->smtpAuthType;
    }

    /**
     * @return bool
     */
    public function isSmtpDebugEnabled(): bool
    {
        return $this->smtpDebugEnabled;
    }

    /**
     * @return array
     */
    public function __debugInfo(): array
    {
        return [
            'id' => $this->id,
            'displayName' => $this->displayName,
            'emailAddress' => $this->emailAddress,
            'descrption' => $this->description,
            'username' => $this->username,
            'password' => '**********',
            'server' => $this->server,
            'imapIsSentFolderActive' => $this->imapIsSentFolderActive,
            'imapSentFolder' => $this->imapSentFolder,
            'imapPort' => $this->imapPort,
            'imapType' => $this->imapType,
            'letterTemplateId' => $this->letterTemplateId,
            'isAutoresponseActive' => $this->isAutoresponseActive,
            'autoresponseSubject' => $this->autoresponseSubject,
            'autoresponseText' => $this->autoresponseText,
            'autoresponseLimitEnabled' => $this->autoResponseLimitEnabled,
            'projectId' => $this->projectId,
            'isBackupEnabled' => $this->isBackupEnabled,
            'backupDeleteAfter' => $this->backupDeleteAfter,
            'addressId' => $this->addressId,
            'companyId' => $this->companyId,
            'isDeleted' => $this->isDeleted,
            'isTicketImportEnabled' => $this->isTicketSystemActive,
            'isTicketDeleteEnabled' => $this->isTicketDeleteEnabled,
            'isTicketMarkAsFinishedEnabled' => $this->isTicketMarkAsFinishedEnabled,
            'ticketQueueId' => $this->ticketQueueId,
            'ticketProjectId' => $this->ticketProjectId,
            'isTicketInboundEmailAddressEnabled' => $this->isTicketInboundEmailAddressEnabled,
            'ticketStartDate' => $this->ticketStartDay,
            'isSmtpEnabled' => $this->isSmtpActive,
            'smtpServer' => $this->smtpServer,
            'smtpSecurity' => $this->smtpSecurity,
            'smtpPort' => $this->smtpPort,
            'smtpSenderEmail' => $this->smtpSenderEmail,
            'smtpSenderName' => $this->smtpSenderName,
            'smtpAuthType' => $this->smtpAuthType,
            'smtpAuthParam' => $this->smtpAuthParam,
            'smtpDebugEnabled' => $this->smtpDebugEnabled,
            'clientAlias' => $this->clientAlias,
            'isSignatureEnabled' => $this->isSignatureActive,
            'signatureText' => $this->signatureText,
            'signatureMutex' => $this->signatureMutex,
        ];
    }
}
