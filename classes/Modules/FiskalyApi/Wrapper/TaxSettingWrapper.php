<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Wrapper;

use erpAPI;

final class TaxSettingWrapper
{
    /** @var erpAPI $erp */
    private $erp;

    /**
     * @param erpAPI $erp
     */
    public function __construct(erpAPI $erp)
    {
        $this->erp = $erp;
    }

    /**
     * @param int $projectId
     *
     * @return float
     */
    public function getStandardTaxRate(int $projectId): float
    {
        return (float)$this->erp->GetStandardSteuersatzNormal($projectId);
    }
}
