<?php

declare(strict_types=1);

namespace Xentral\Modules\Ebay\Service;

use Xentral\Components\Database\Database;
use Xentral\Modules\Ebay\Data\StockLoggingData;

final class EbayStockLoggingService
{
    /** @var Database $db */
    private $db;

    /**
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->db = $database;
    }

    public function saveStockLoggingInformation(
        int $shopId,
        string $jobIdExternal,
        StockLoggingData $stockLoggingData
    ): void {
        $sql = 'INSERT INTO `ebay_stock_logging` (`shop_id`, `job_id_external`, `listing_id_external`,`sku`, `quantity`, `status`) 
                VALUES (:shop_id, :job_id_external, :listing_id_external, :sku, :quantity, :status)';
        $values = [
            'shop_id'         => $shopId,
            'job_id_external' => $jobIdExternal,
            'listing_id_external' => $stockLoggingData->getItemId(),
            'sku'             => $stockLoggingData->getSku(),
            'quantity'        => $stockLoggingData->getQuantity(),
            'status'          => $stockLoggingData->getStatus(),
        ];
        $this->db->perform($sql, $values);
        $stockLoggingDataId = $this->db->lastInsertId();

        foreach ($stockLoggingData->getVariations() as $variation) {
            $sql = 'INSERT INTO `ebay_stock_logging_variations` (`ebay_stock_logging_id`, `sku`, `quantity`)
                    VALUES (:ebay_stock_logging_id, :sku, :quantity)';
            $values = [
                'ebay_stock_logging_id' => $stockLoggingDataId,
                'sku'                   => $variation->getSku(),
                'quantity'              => $variation->getQuantity(),
            ];
            $this->db->perform($sql, $values);
        }

        foreach ($stockLoggingData->getErrorMessages() as $errorMessage => $type) {
            $sql = 'INSERT INTO `ebay_stock_logging_errors` (`ebay_stock_logging_id`, `message`, `type`)
                    VALUES (:ebay_stock_logging_id, :message, :type)';
            $values = [
                'ebay_stock_logging_id' => $stockLoggingDataId,
                'message'               => $errorMessage,
                'type'                  => $type,
            ];
            $this->db->perform($sql, $values);
        }
    }
}
