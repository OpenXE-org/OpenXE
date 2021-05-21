<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Data\Transaction;

use DateTimeInterface;
use DateTime;
use DateTimeZone;
use stdClass;
use Xentral\Modules\FiskalyApi\Data\MetaData;

class TransactionReponse extends Transaction
{
    /** @var int|null $number */
    private $number;

    /** @var string|null */
    private $qrCodeData;

    /** @var DateTimeInterface|null $timeStart */
    private $timeStart;

    /** @var DateTimeInterface|null $timeStart */
    private $timeEnd;

    /** @var string|null $clientSerialNumber */
    private $clientSerialNumber;

    /** @var string|null $certificateSerial */
    private $certificateSerial;

    /** @var int|null $revision */
    private $revision;

    /** @var int|null $latestRevision */
    private $latestRevision;

    /** @var TransactionLog|null $log */
    private $log;

    /** @var TransactionSignature|null $signature */
    private $signature;

    /** @var string|null $tssId */
    private $tssId;

    /** @var string|null $_type */
    private $_type;

    /** @var string|null $_id */
    private $_id;

    /** @var string|null $_env */
    private $_env;

    /** @var string|null $_version */
    private $_version;


    public function __construct(
        string $state,
        string $clientId,
        ?TransactionSchema $schema = null,
        ?MetaData $metaData = null,
        ?int $number = null,
        ?DateTimeInterface $timeStart = null,
        ?DateTimeInterface $timeEnd = null,
        ?string $clientSerialNumber = null,
        ?string $certificateSerial = null,
        ?string $qrCodeData = null,
        ?int $revision = null,
        ?int $latestRevision = null,
        ?TransactionLog $log = null,
        ?TransactionSignature $signature = null,
        ?string $tssId = null,
        ?string $type = null,
        ?string $uuId = null,
        ?string $env = null,
        ?string $vesion = null
    ) {
        parent::__construct($state, $clientId, $schema, $metaData);
        $this->setNumber($number);
        $this->setTimeStart($timeStart);
        $this->setTimeEnd($timeEnd);
        $this->setClientSerialNumber($clientSerialNumber);
        $this->setCertificateSerial($certificateSerial);
        $this->setQrCodeData($qrCodeData);
        $this->setRevision($revision);
        $this->setLatestRevision($latestRevision);
        $this->setLog($log);
        $this->setSignature($signature);
        $this->setTssId($tssId);
        $this->setType($type);
        $this->setId($uuId);
        $this->setEnv($env);
        $this->setVersion($vesion);
    }

    /**
     * @param $apiResult
     *
     * @throws \Exception
     * @return Transaction
     */
    public static function fromApiResult(object $apiResult): TransactionReponse
    {
        return new self(
            $apiResult->state,
            $apiResult->client_id,
            empty($apiResult->schema) ? null : TransactionSchema::fromApiResult($apiResult->schema),
            empty($apiResult->metadata) ? null : MetaData::fromApiResult($apiResult->metadata),
            isset($apiResult->number) ? (int)$apiResult->number : null,
            !empty($apiResult->time_start) ? (new DateTime('now', new DateTimeZone('UTC')))->setTimestamp(
                $apiResult->time_start
            ) : null,
            !empty($apiResult->time_end) ? (new DateTime('now', new DateTimeZone('UTC')))->setTimestamp(
                $apiResult->time_end
            ) : null,
            $apiResult->client_serial_number ?? null,
            $apiResult->certificate_serial ?? null,
            $apiResult->qr_code_data ?? null,
            isset($apiResult->revision) ? (int)$apiResult->revision : null,
            isset($apiResult->latest_revision) ? (int)$apiResult->latest_revision : null,
            !empty($apiResult->log) ? TransactionLog::fromApiResult($apiResult->log) : null,
            !empty($apiResult->signature) ? TransactionSignature::fromApiResult($apiResult->signature) : null,
            $apiResult->tss_id ?? null,
            $apiResult->_type ?? null,
            $apiResult->_id ?? null,
            $apiResult->_env ?? null,
            $apiResult->_version ?? null
        );
    }

    /**
     * @param array $dbState
     *
     * @return Transaction
     */
    public static function fromDbState(array $dbState): TransactionReponse
    {
        return new self(
            $dbState['state'],
            $dbState['client_id'],
            empty($dbState['schema']) ? null : TransactionSchema::fromDbState($dbState['schema']),
            !isset($dbState['metadata']) ? null : MetaData::fromDbState($dbState['metadata']),
            isset($dbState['number']) ? (int)$dbState['number'] : null,
            !empty($dbState['time_start']) ? (new DateTime('now', new DateTimeZone('UTC')))->setTimestamp(
                $dbState['time_start']
            ) : null,
            !empty($dbState['time_end']) ? (new DateTime('now', new DateTimeZone('UTC')))->setTimestamp(
                $dbState['time_end']
            ) : null,
            $dbState['client_serial_number'] ?? null,
            $dbState['certificate_serial'] ?? null,
            $dbState['qr_code_data'] ?? null,
            isset($dbState['revision']) ? (int)$dbState['revision'] : null,
            isset($dbState['latest_revision']) ? (int)$dbState['latest_revision'] : null,
            !empty($dbState['log']) ? TransactionLog::fromDbState($dbState['log']) : null,
            !empty($dbState['signature']) ? TransactionSignature::fromDbState($dbState['signature']) : null,
            $dbState['tss_id'] ?? null,
            $dbState['_type'] ?? null,
            $dbState['_id'] ?? null,
            $dbState['_env'] ?? null,
            $dbState['_version'] ?? null
        );
    }

    public function toArray(): array
    {
        $dbState = parent::toArray();
        if ($this->number !== null) {
            $dbState['number'] = $this->getNumber();
        }
        if ($this->timeStart !== null) {
            $dbState['time_start'] = $this->timeStart->getTimestamp();
        }
        if ($this->timeEnd !== null) {
            $dbState['time_end'] = $this->timeEnd->getTimestamp();
        }
        if ($this->clientSerialNumber !== null) {
            $dbState['client_serial_number'] = $this->getClientSerialNumber();
        }
        if ($this->certificateSerial !== null) {
            $dbState['certificate_serial'] = $this->getCertificateSerial();
        }
        if ($this->qrCodeData !== null) {
            $dbState['qr_code_data'] = $this->getQrCodeData();
        }
        if ($this->revision !== null) {
            $dbState['revision'] = $this->getRevision();
        }
        if ($this->latestRevision !== null) {
            $dbState['latest_revision'] = $this->getLatestRevision();
        }
        if ($this->tssId !== null) {
            $dbState['tss_id'] = $this->getTssId();
        }
        if ($this->log !== null) {
            $dbState['log'] = $this->log->toArray();
        }
        if ($this->signature !== null) {
            $dbState['signature'] = $this->signature->toArray();
        }
        if ($this->tssId !== null) {
            $dbState['tss_id'] = $this->getTssId();
        }
        if ($this->metaData !== null) {
            $dbState['metadata'] = $this->metaData->toArray();
        }
        if ($this->_type !== null) {
            $dbState['_type'] = $this->getType();
        }
        if ($this->_id !== null) {
            $dbState['_id'] = $this->getId();
        }
        if ($this->_version !== null) {
            $dbState['_version'] = $this->getVersion();
        }
        if ($this->_env !== null) {
            $dbState['_env'] = $this->getEnv();
        }

        return $dbState;
    }

    /**
     * @return stdClass
     */
    public function toApiResult()
    {
        $apiResult = parent::toApiResult();
        if ($this->number !== null) {
            $apiResult->number = $this->getNumber();
        }
        if ($this->timeStart !== null) {
            $apiResult->time_start = $this->timeStart->getTimestamp();
        }
        if ($this->timeEnd !== null) {
            $apiResult->time_end = $this->timeEnd->getTimestamp();
        }
        if ($this->clientSerialNumber !== null) {
            $apiResult->client_serial_number = $this->getClientSerialNumber();
        }
        if ($this->certificateSerial !== null) {
            $apiResult->certificate_serial = $this->getCertificateSerial();
        }
        if ($this->qrCodeData !== null) {
            $apiResult->qr_code_data = $this->getQrCodeData();
        }
        if ($this->revision !== null) {
            $apiResult->revision = $this->getRevision();
        }
        if ($this->latestRevision !== null) {
            $apiResult->latest_revision = $this->getLatestRevision();
        }
        if ($this->tssId !== null) {
            $apiResult->tss_id = $this->getTssId();
        }
        if ($this->log !== null) {
            $apiResult->log = $this->log->toApiResult();
        }
        if ($this->signature !== null) {
            $apiResult->signature = $this->signature->toApiResult();
        }
        if ($this->tssId !== null) {
            $apiResult->tss_id = $this->getTssId();
        }
        if ($this->metaData !== null) {
            $apiResult->metadata = $this->metaData->toApiResult();
        }
        if ($this->_type !== null) {
            $apiResult->_type = $this->getType();
        }
        if ($this->_id !== null) {
            $apiResult->_id = $this->getId();
        }
        if ($this->_version !== null) {
            $apiResult->_version = $this->getVersion();
        }
        if ($this->_env !== null) {
            $apiResult->_env = $this->getEnv();
        }

        return $apiResult;
    }

    /**
     * @return int|null
     */
    public function getNumber(): ?int
    {
        return $this->number;
    }

    /**
     * @param int|null $number
     */
    public function setNumber(?int $number): void
    {
        $this->number = $number;
    }

    /**
     * @return string|null
     */
    public function getQrCodeData(): ?string
    {
        return $this->qrCodeData;
    }

    /**
     * @param string|null $qrCodeData
     */
    public function setQrCodeData(?string $qrCodeData): void
    {
        $this->qrCodeData = $qrCodeData;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getTimeStart(): ?DateTimeInterface
    {
        return $this->timeStart;
    }

    /**
     * @param DateTimeInterface|null $timeStart
     */
    public function setTimeStart(?DateTimeInterface $timeStart): void
    {
        $this->timeStart = $timeStart;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getTimeEnd(): ?DateTimeInterface
    {
        return $this->timeEnd;
    }

    /**
     * @param DateTimeInterface|null $timeEnd
     */
    public function setTimeEnd(?DateTimeInterface $timeEnd): void
    {
        $this->timeEnd = $timeEnd;
    }

    /**
     * @return string|null
     */
    public function getClientSerialNumber(): ?string
    {
        return $this->clientSerialNumber;
    }

    /**
     * @param string|null $clientSerialNumber
     */
    public function setClientSerialNumber(?string $clientSerialNumber): void
    {
        $this->clientSerialNumber = $clientSerialNumber;
    }

    /**
     * @return string|null
     */
    public function getCertificateSerial(): ?string
    {
        return $this->certificateSerial;
    }

    /**
     * @param string|null $certificateSerial
     */
    public function setCertificateSerial(?string $certificateSerial): void
    {
        $this->certificateSerial = $certificateSerial;
    }

    /**
     * @return int|null
     */
    public function getRevision(): ?int
    {
        return $this->revision;
    }

    /**
     * @param int|null $revision
     */
    public function setRevision(?int $revision): void
    {
        $this->revision = $revision;
    }

    /**
     * @return int|null
     */
    public function getLatestRevision(): ?int
    {
        return $this->latestRevision;
    }

    /**
     * @param int|null $latestRevision
     */
    public function setLatestRevision(?int $latestRevision): void
    {
        $this->latestRevision = $latestRevision;
    }

    /**
     * @return TransactionLog|null
     */
    public function getLog(): ?TransactionLog
    {
        return $this->log;
    }

    /**
     * @param TransactionLog|null $log
     */
    public function setLog(?TransactionLog $log): void
    {
        $this->log = $log;
    }

    /**
     * @return TransactionSignature|null
     */
    public function getSignature(): ?TransactionSignature
    {
        return $this->signature;
    }

    /**
     * @param TransactionSignature|null $signature
     */
    public function setSignature(?TransactionSignature $signature): void
    {
        $this->signature = $signature;
    }

    /**
     * @return string|null
     */
    public function getTssId(): ?string
    {
        return $this->tssId;
    }

    /**
     * @param string|null $tssId
     */
    public function setTssId(?string $tssId): void
    {
        $this->tssId = $tssId;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->_type;
    }

    /**
     * @param string|null $type
     */
    public function setType(?string $type): void
    {
        $this->_type = $type;
    }

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->_id;
    }

    /**
     * @param string|null $id
     */
    public function setId(?string $id): void
    {
        $this->_id = $id;
    }

    /**
     * @return string|null
     */
    public function getEnv(): ?string
    {
        return $this->_env;
    }

    /**
     * @param string|null $env
     */
    public function setEnv(?string $env): void
    {
        $this->_env = $env;
    }

    /**
     * @return string|null
     */
    public function getVersion(): ?string
    {
        return $this->_version;
    }

    /**
     * @param string|null $version
     */
    public function setVersion(?string $version): void
    {
        $this->_version = $version;
    }
}
