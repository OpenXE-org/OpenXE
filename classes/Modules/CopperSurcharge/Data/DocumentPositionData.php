<?php

declare(strict_types=1);

namespace Xentral\Modules\CopperSurcharge\Data;

final class DocumentPositionData
{

    /** @var int $positionId */
    private $positionId;

    /** @var int $articleId */
    private $articleId;

    /** @var string $currency */
    private $currency;

    /**
     * @param int    $positionId
     * @param int    $articleId
     * @param string $currency
     */
    public function __construct(
        int $positionId,
        int $articleId,
        string $currency
    ) {
        $this->positionId = $positionId;
        $this->articleId = $articleId;
        $this->currency = $currency;
    }

    /**
     * @return int
     */
    public function getPositionId(): int
    {
        return $this->positionId;
    }

    /**
     * @return int
     */
    public function getArticleId(): int
    {
        return $this->articleId;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }
}
