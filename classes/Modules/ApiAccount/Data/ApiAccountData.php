<?php

declare(strict_types=1);

namespace Xentral\Modules\ApiAccount\Data;

final class ApiAccountData
{
    /** @var int $id */
    private $id;

    /** @var string $name */
    private $name;

    /** @var string $initKey */
    private $initKey;

    /** @var string $importQueueName */
    private $importQueueName;

    /** @var string $eventUrl */
    private $eventUrl;

    /** @var string $remoteDomain */
    private $remoteDomain;

    /** @var bool $active */
    private $active;

    /** @var bool $importQueueActive */
    private $importQueueActive;

    /** @var bool $cleanUtf8Active */
    private $cleanUtf8Active;

    /** @var int $transferAccountId */
    private $transferAccountId;

    /** @var int $projectId */
    private $projectId;

    /** @var string $permissions */
    private $permissions;

    /** @var bool $isLegacy */
    private $isLegacy;

    /** @var bool */
    private $isHtmlTransformation;


    /**
     * @param int         $apiAccountId
     * @param string      $name
     * @param string      $initKey
     * @param string      $importQueueName
     * @param string      $eventUrl
     * @param string      $remoteDomain
     * @param bool        $active
     * @param bool        $importQueueActive
     * @param bool        $cleanUtf8Active
     * @param int         $transferAccountId
     * @param int         $projectId
     * @param string|null $permissions
     * @param bool        $isLegacy
     * @param bool        $isHtmlTransformation
     */
    public function __construct(
        int $apiAccountId,
        string $name,
        string $initKey,
        string $importQueueName,
        string $eventUrl,
        string $remoteDomain,
        bool $active,
        bool $importQueueActive,
        bool $cleanUtf8Active,
        int $transferAccountId,
        int $projectId,
        ?string $permissions,
        bool $isLegacy,
        bool $isHtmlTransformation
    ) {
        $this->id = $apiAccountId;
        $this->name = $name;
        $this->initKey = $initKey;
        $this->importQueueName = $importQueueName;
        $this->eventUrl = $eventUrl;
        $this->remoteDomain = $remoteDomain;
        $this->active = $active;
        $this->importQueueActive = $importQueueActive;
        $this->cleanUtf8Active = $cleanUtf8Active;
        $this->transferAccountId = $transferAccountId;
        $this->projectId = $projectId;
        $this->permissions = $permissions;
        $this->isLegacy = $isLegacy;
        $this->isHtmlTransformation = $isHtmlTransformation;
    }

    /**
     * @param array $formData
     *
     * @return ApiAccountData
     */
    public static function fromFormData(array $formData): ApiAccountData
    {
        $apiAccountData = new self(
            $formData['id'], $formData['name'], $formData['init_key'], $formData['import_queue_name'],
            $formData['event_url'], $formData['remotedomain'], $formData['active'],
            $formData['import_queue'], $formData['cleanutf8'], $formData['transfer_account_id'],
            $formData['project_id'], $formData['permissions'], $formData['is_legacy'], $formData['is_html_transformation']
        );

        return $apiAccountData;
    }

    /**
     * @param array $apiAccountRow
     *
     * @return ApiAccountData
     */
    public static function fromDbState(array $apiAccountRow): ApiAccountData
    {
        $apiAccountData = new self(
            $apiAccountRow['id'], $apiAccountRow['bezeichnung'], $apiAccountRow['initkey'],
            $apiAccountRow['importwarteschlange_name'], $apiAccountRow['event_url'],
            $apiAccountRow['remotedomain'], (bool) $apiAccountRow['aktiv'],
            (bool) $apiAccountRow['importwarteschlange'], (bool) $apiAccountRow['cleanutf8'],
            $apiAccountRow['uebertragung_account'], $apiAccountRow['projekt'],
            $apiAccountRow['permissions'],
            (bool) $apiAccountRow['is_legacy'], (bool) $apiAccountRow['ishtmltransformation']
        );

        return $apiAccountData;
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
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getInitKey(): string
    {
        return $this->initKey;
    }

    /**
     * @return string
     */
    public function getImportQueueName(): string
    {
        return $this->importQueueName;
    }

    /**
     * @return string
     */
    public function getEventUrl(): string
    {
        return $this->eventUrl;
    }

    /**
     * @return string
     */
    public function getRemoteDomain(): string
    {
        return $this->remoteDomain;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @return bool
     */
    public function isImportQueueActive(): bool
    {
        return $this->importQueueActive;
    }

    /**
     * @return bool
     */
    public function isCleanUtf8Active(): bool
    {
        return $this->cleanUtf8Active;
    }

    /**
     * @return int
     */
    public function getTransferAccountId(): int
    {
        return $this->transferAccountId;
    }

    /**
     * @return int
     */
    public function getProjectId(): int
    {
        return $this->projectId;
    }

    /**
     * @return string|null
     */
    public function getPermissions(): ?string
    {
        return $this->permissions;
    }

    /**
     * @return bool
     */
    public function isLegacy(): bool
    {
        return $this->isLegacy;
    }

    public function isHtmlTransformationActive(): bool
    {
        return $this->isHtmlTransformation;
    }
}
