<?php

declare(strict_types=1);

namespace Xentral\Modules\Log\Wrapper;

use erpAPI;

/**
 * Anti-Corruption-Layer für erp::GetKonfiguration und erp::SetKonfigurationValue
 */
final class CompanyConfigWrapper
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
     * @param string $name
     *
     * @return mixed
     */
    public function get(string $name)
    {
        return $this->erp->GetKonfiguration($name);
    }

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return void
     */
    public function set(string $name, $value)
    {
        $this->erp->SetKonfigurationValue($name, $value);
    }
}
