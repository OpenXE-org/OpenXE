<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Data\CashPointClosing;

class CashPointClosingTransactionUser
{
    /** @var string $userExportId */
    private $userExportId;

    /** @var string|null $name */
    private $name;

    /**
     * CashPointClosingTransactionUser constructor.
     *
     * @param string      $userExportId
     * @param string|null $name
     */
    public function __construct(string $userExportId, ?string $name = null)
    {
        $this->userExportId = $userExportId;
        $this->name = $name;
    }

    /**
     * @param $apiResult
     *
     * @return static
     */
    public static function fromApiResult(object $apiResult): self
    {
        return new self($apiResult->user_export_id, $apiResult->name ?? null);
    }

    /**
     * @param array $dbState
     *
     * @return static
     */
    public static function fromDbState(array $dbState): self
    {
         return new self($dbState['user_export_id'], $dbState['name'] ?? null);
    }

    /**
     * @return null[]|string[]
     */
    public function toArray(): array
    {
        $dbState = ['user_export_id' => $this->getUserExportId()];
        if ($this->name !== null) {
            $dbState['name'] = $this->getName();
        }

        return $dbState;
    }

    /**
     * @return string
     */
    public function getUserExportId(): string
    {
        return $this->userExportId;
    }

    /**
     * @param string $userExportId
     */
    public function setUserExportId(string $userExportId): void
    {
        $this->userExportId = $userExportId;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }
}
