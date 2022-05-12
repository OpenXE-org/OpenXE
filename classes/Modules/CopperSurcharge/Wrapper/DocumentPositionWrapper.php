<?php

declare(strict_types=1);

namespace Xentral\Modules\CopperSurcharge\Wrapper;

use erpAPI;
use Xentral\Components\Database\Database;


final class DocumentPositionWrapper implements DocumentPositionWrapperInterface
{

    /** @var erpAPI $erp */
    private $erp;

    /** @var Database $db */
    private $db;

    /**
     * @param erpAPI   $erp
     * @param Database $db
     */
    public function __construct(erpAPI $erp, Database $db)
    {
        $this->erp = $erp;
        $this->db = $db;
    }

    /**
     * @param string $doctype
     * @param int    $docId
     * @param int    $articleId
     * @param array  $articleData
     * @param float  $amount
     * @param float  $price
     * @param string $currency
     * @param string $description
     *
     * @return int
     */
    public function addPositionManuallyWithPrice(
        string $doctype,
        int $docId,
        int $articleId,
        array $articleData,
        float $amount,
        float $price,
        string $currency = 'EUR',
        string $description = ''

    ): int {
        $posId = $this->erp->AddPositionManuellPreis(
            $doctype,
            $docId,
            $articleId,
            $amount,
            $articleData['name_de'],
            $price,
            $articleData['vat'],
            $articleData['discount'],
            $currency,
            $description
        );

        if (empty($posId)) {
            return 0;
        }

        return (int)$posId;
    }
}
