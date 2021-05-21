<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Data\Transaction;

use Xentral\Modules\FiskalyApi\UuidTool;

class TransactionRequest extends Transaction
{
    /** @var string|null $tssId */
    private $tssId;

    /** @var string|null $_id */
    private $_id;

    /** @var int $revision */
    private $revision;

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
    public function setTssId(?string $tssId): self
    {
        $this->tssId = $tssId;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        if($this->_id !== null) {
            return $this->_id;
        }
        $this->setId(UuidTool::generateUuid());

        return $this->_id;
    }

    /**
     * @param string|null $id
     */
    public function setId(?string $id): self
    {
        $this->_id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getRevision(): int
    {
        return $this->revision;
    }

    /**
     * @param int $revision
     */
    public function setRevision(int $revision): self
    {
        $this->revision = $revision;

        return $this;
    }
}
