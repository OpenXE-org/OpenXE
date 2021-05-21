<?php

declare(strict_types=1);

namespace Xentral\Modules\SubscriptionCycle;

use DateTimeInterface;

interface SubscriptionModuleInterface
{
    /**
     * @param int    $customer
     * @param string $documentType
     *
     * @return mixed
     */
    public function RechnungKunde($customer, $documentType);

    /**
     * @param $customer
     * @param $invoiceGroupKey
     * @param $key
     *
     * @return mixed
     */
    public function AuftragImportAbo($customer, $invoiceGroupKey, $key);

    /**
     * @param $customer
     * @param $invoiceGroupKey
     * @param $key
     *
     * @return mixed
     */
    public function RechnungImportAbo($customer, $invoiceGroupKey, $key);

    /**
     * @param string                 $documentType
     *
     * @return array|null
     */
    public function GetRechnungsArray($documentType);
}
