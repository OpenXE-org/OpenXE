<?php

namespace Xentral\Modules\AmazonVendorDF;

use Xentral\Components\Database\Database;
use Xentral\Modules\AmazonVendorDF\Data\PurchaseOrder;
use Xentral\Modules\AmazonVendorDF\Models\PurchaseOrderInformation;
use Xentral\Modules\AmazonVendorDF\Exception\ColumnNotFoundException;
use Xentral\Modules\AmazonVendorDF\Exception\DuplicatePurchaseOrderException;
use Xentral\Modules\AmazonVendorDF\Exception\PurchaseOrderNumberNotFoundException;

class PurchaseOrderInformationRepository
{
    /** @var string */
    private $tableName = 'amazon_vendor_df_purchase_orders';

    private $columns = [
        'status',
        'external_id',
        'raw',
        'order_id',
        'acknowledged',
        'acknowledgement_transaction_id',
        'shipping_label_requested',
        'shipping_label_request_transaction_id',
        'shipping_label_data',
        'shipment_confirmation_transaction_id',
        'created_at',
        'updated_at',
        'shopexport_id',
    ];

    /** @var Database */
    private $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function createPurchaseOrderInformation(PurchaseOrder $purchaseOrder, int $shopExportId):void
    {
        if ($this->doesPurchaseOrderInformationExist($purchaseOrder->getPurchaseOrderNumber())) {
            throw new DuplicatePurchaseOrderException();
        }

        $statement = $this->database
            ->insert()
            ->into($this->tableName)
            ->cols(['external_id', 'raw', 'shopexport_id'])
            ->getStatement();



        $values = [
            'external_id'   => $purchaseOrder->getPurchaseOrderNumber(),
            'raw'           => json_encode($purchaseOrder->getRawData()),
            'shopexport_id' => json_encode($shopExportId),
        ];
        $this->database->perform($statement, $values);
    }

    public function doesPurchaseOrderInformationExist(string $purchaseOrderNumber): bool
    {
        $statement = $this->database
            ->select()
            ->cols(['id'])
            ->from($this->tableName)
            ->where('external_id = :external_id')
            ->bindValue('external_id', $purchaseOrderNumber)
            ->limit(1);

        $purchaseOrderId = $this->database->fetchRow($statement->getStatement(), $statement->getBindValues());

        return !empty($purchaseOrderId['id']);
    }

    public function countPurchaseOrdersWaitingForImport(int $shopExportId): int
    {
        $statement = $this->database
            ->select()
            ->cols(['id'])
            ->from($this->tableName)
            ->where('shopexport_id = :shopexport_id')
            ->where('acknowledged = 0')
            ->where('status IS NULL')
            ->bindValue('shopexport_id', $shopExportId);

        return count($this->database->fetchAssoc($statement->getStatement(), $statement->getBindValues()));
    }

    public function getNextPurchaseOrderInformationToImport(int $shopExportId): PurchaseOrderInformation
    {
        $statement = $this->database
            ->select()
            ->cols($this->columns)
            ->from($this->tableName)
            ->where('shopexport_id = :shopexport_id')
            ->where('acknowledged = 0')
            ->where('status IS NULL') //TODO Konstante verwenden
            ->bindValue('shopexport_id', $shopExportId)
            ->orderBy(['created_at ASC'])
            ->limit(1);

        $data = $this->database->fetchRow($statement->getStatement(), $statement->getBindValues());

        return $this->buildPurchaseOrderInformation($data);
    }

    /**
     * @param array $constraints
     *
     * @return PurchaseOrderInformation[]
     */
    public function listPurchaseOrderInformation(array $constraints = []): array
    {
        $statement = $this->database
            ->select()
            ->cols($this->columns)
            ->from($this->tableName);

        foreach ($constraints as $constraint) {
            $column = $constraint[0];
            if(!in_array($column,$this->columns, false)){
                throw new ColumnNotFoundException("Column '{$column}' does not exist in table {$this->tableName}");
            }
            $value = $constraint[1];
            if ($value === null) {
                $statement->where("{$column} IS NULL");
                continue;
            }

            $operator = empty($constraint[2]) ? '=' : $constraint[2];

            if ($constraint[1] instanceof \DateTime) {
                $statement->where("{$column} {$operator} :{$column}");
                $statement->bindValue($column, $value->format('Y-m-d H:i:s'));
            } else {
                $statement->where("{$column} {$operator} :{$column}");
                $statement->bindValue($column, $value);
            }
        }

        return array_map(
            function (array $data) {
                return $this->buildPurchaseOrderInformation($data);
            },
            $this->database->fetchAll($statement->getStatement(), $statement->getBindValues())
        );
    }


    public function savePurchaseOrderInformation(PurchaseOrderInformation $purchaseOrderInformation): PurchaseOrderInformation
    {
        if (empty($purchaseOrderInformation->getPurchaseOrderNumber())) {
            //TODO Throw
        }

        $columnsToSave = [];
        $columnsToIgnore = ['raw', 'created_at', 'updated_at', 'shopexport_id'];
        foreach ($this->columns as $column){
            if(!in_array($column,$columnsToIgnore)){
                $columnsToSave[] = $column;
            }
        }

        $statement = $this->database
            ->update()
            ->cols($columnsToSave)
            ->table($this->tableName)
            ->where('external_id = :external_id')
            ->bindValue('external_id', $purchaseOrderInformation->getPurchaseOrderNumber())
            ->bindValues(
                [
                    'order_id'                                  => $purchaseOrderInformation->getOrderId(),
                    'status'                                    => $purchaseOrderInformation->getStatus(),
                    'acknowledged'                              => $purchaseOrderInformation->isAcknowledged(),
                    'acknowledgement_transaction_id'            => $purchaseOrderInformation->getAcknowledgementTransactionId(),
                    'shipping_label_requested'                  => $purchaseOrderInformation->isShippingLabelRequested(),
                    'shipping_label_request_transaction_id'     => $purchaseOrderInformation->getShippingLabelRequestTransactionId(),
                    'shipping_label_data'                       => $purchaseOrderInformation->getShippingLabelData(),
                    'shipment_confirmation_transaction_id'      => $purchaseOrderInformation->getShipmentConfirmationTransactionId(),
                ]
            );

        $this->database->perform($statement, $statement->getBindValues());

        $purchaseOrderInformation->setUpdatedAt(new \DateTime());

        return $purchaseOrderInformation;
    }


    public function getPurchaseOrderInformationByPurchaseOrderNumber(string $purchaseOrderNumber): PurchaseOrderInformation
    {
        $statement = $this->database
            ->select()
            ->cols($this->columns)
            ->from($this->tableName)
            ->where('external_id = :external_id')
            ->bindValue('external_id', $purchaseOrderNumber)
            ->limit(1);

        $purchaseOrderData = $this->database->fetchRow($statement->getStatement(), $statement->getBindValues());

        return $this->buildPurchaseOrderInformation($purchaseOrderData);
    }

    public function getPurchaseOrderInformationByOrderId(int $orderId): PurchaseOrderInformation
    {
        $statement = $this->database
            ->select()
            ->cols($this->columns)
            ->from($this->tableName)
            ->where('order_id = :order_id')
            ->bindValue('order_id', $orderId)
            ->limit(1);

        $purchaseOrderData = $this->database->fetchRow($statement->getStatement(), $statement->getBindValues());

        return $this->buildPurchaseOrderInformation($purchaseOrderData);
    }

    private function buildPurchaseOrderInformation(array $data): PurchaseOrderInformation
    {
        if(empty($data['external_id'])){
            throw new PurchaseOrderNumberNotFoundException();
        }

        $purchaseOrderStatus = new PurchaseOrderInformation($data['external_id']);
        if (isset($data['raw'])) {
            $purchaseOrderStatus->setRaw($data['raw']);
        }
        if (isset($data['order_id'])) {
            $purchaseOrderStatus->setOrderId($data['order_id']);
        }
        if (isset($data['acknowledged'])) {
            $purchaseOrderStatus->setAcknowledged((bool)$data['acknowledged']);
        }
        if (isset($data['acknowledgement_transaction_id'])) {
            $purchaseOrderStatus->setAcknowledgementTransactionId($data['acknowledgement_transaction_id']);
        }
        if (isset($data['shipping_label_requested'])) {
            $purchaseOrderStatus->setShippingLabelRequested((bool)$data['shipping_label_requested']);
        }
        if (isset($data['shipping_label_request_transaction_id'])) {
            $purchaseOrderStatus->setShippingLabelRequestTransactionId($data['shipping_label_request_transaction_id']);
        }
        if (isset($data['shipping_label_data'])) {
            $purchaseOrderStatus->setShippingLabelData($data['shipping_label_data']);
        }
        if (isset($data['shipment_confirmation_transaction_id'])) {
            $purchaseOrderStatus->setAcknowledged($data['shipment_confirmation_transaction_id']);
        }
        if (isset($data['created_at'])) {
            $purchaseOrderStatus->setCreatedAt(\DateTime::createFromFormat('Y-m-d H:i:s', $data['created_at']));
        }
        if (isset($data['updated_at'])) {
            $purchaseOrderStatus->setUpdatedAt(\DateTime::createFromFormat('Y-m-d H:i:s', $data['updated_at']));
        }
        if (isset($data['status'])) {
            $purchaseOrderStatus->setStatus($data['status']);
        }

        return $purchaseOrderStatus;
    }
}
