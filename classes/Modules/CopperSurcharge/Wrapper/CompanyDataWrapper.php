<?php

declare(strict_types=1);

namespace Xentral\Modules\CopperSurcharge\Wrapper;

use erpAPI;


final class CompanyDataWrapper
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
     * @param string $fieldName
     *
     * @return string
     */
    public function getCompanyData(string $fieldName): string
    {
        return (string)$this->erp->Firmendaten($fieldName);
    }
}
