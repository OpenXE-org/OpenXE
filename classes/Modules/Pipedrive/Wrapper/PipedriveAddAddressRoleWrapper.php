<?php

declare(strict_types=1);

namespace Xentral\Modules\Pipedrive\Wrapper;

use erpAPI;

final class PipedriveAddAddressRoleWrapper
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
     * @param int $contactId
     * @param int $groupId
     *
     * @return void
     */
    public function add(int $contactId, int $groupId): void
    {
        $this->erp->AddRolleZuAdresse($contactId, 'Mitglied', 'von', 'Gruppe', $groupId);
    }
}
