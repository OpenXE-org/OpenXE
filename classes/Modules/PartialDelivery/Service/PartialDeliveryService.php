<?php

declare(strict_types=1);

namespace Xentral\Modules\PartialDelivery\Service;

use Xentral\Components\Database\Database;

class PartialDeliveryService
{
    private const PARTIAL_DELIVERY_SETUP_FIELDS = [
        'aktion',
        'internet',
        'internebezeichnung',
        'art',
        'ust_ok',
        'keinsteuersatz',
        'ustid',
        'projekt',
        'aktion',
        'angebotid',
        'angebot',
        'ihrebestellnummer',
        'standardlager',
        'kommissionskonsignationslager',
        'shopextid',
        'shop',
        'shopextstatus',
    ];

    /** @var Database $database */
    private $database;

    /**
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    /**
     * @param int $partialDeliveryId
     *
     * @return int
     */
    public function getParentIdRecursive(int $partialDeliveryId): int
    {
        $timeout = 0;
        $parentId = $partialDeliveryId;
        $sql = 'SELECT `teillieferungvon` FROM `auftrag` WHERE `id`= :parent_id LIMIT 1';
        while ($timeout < 100) {
            $currentParentId = (int)$this->database->fetchValue($sql, ['parent_id' => $parentId]);
            if ($currentParentId <= 0) {
                break;
            }
            $parentId = $currentParentId;
            $timeout++;
        }

        return $parentId;
    }

    /**
     * @param int $deliveryNoteId
     *
     * @return array
     */
    public function getOrderToDeliveryNotePositionsMap(int $deliveryNoteId): array
    {
        $sqlStatement = 'SELECT lp.auftrag_position_id, lp.id 
                        FROM `lieferschein_position` AS `lp`
                        WHERE lp.lieferschein = :delivery_note_id AND lp.auftrag_position_id > 0
                        ORDER by lp.sort';
        return $this->database->fetchPairs($sqlStatement, ['delivery_note_id' => $deliveryNoteId]);
    }

    /**
     * @param array $deliveryNotePositionsDelta
     * @param array $orderToDeliveryNotePositionMap
     *
     * @return array
     */
    public function mapDeltaToOrderPositions(
        array $deliveryNotePositionsDelta,
        array $orderToDeliveryNotePositionMap
    ): array
    {
        $orderPositionsDelta = [];
        $flippedMap = array_flip($orderToDeliveryNotePositionMap);
        foreach ($deliveryNotePositionsDelta as $key => $value) {
            if (in_array($key, $orderToDeliveryNotePositionMap, false)) {
                $orderPositionsDelta[$flippedMap[$key]] = $value;
            }
        }

        return $orderPositionsDelta;
    }

    /**
     * @param int   $orderId
     * @param array $data
     *
     * @return void
     */
    private function updatePartialDelivery(int $orderId, array $data): void
    {
        $update = ($this->database->update())
            ->table('auftrag')
            ->cols($data)
            ->where('id = :order_id')
            ->getStatement();
        $data['order_id'] = $orderId;
        $this->database->perform($update, $data);
    }

    /**
     * @param int $originalOrderId
     * @param int $partialDeliveryId
     *
     * @return int
     */
    public function setupPartialDeliveryAfterCreation(int $originalOrderId, int $partialDeliveryId): int
    {
        $setupValues = $this->getSetupValues($originalOrderId);
        $this->updatePartialDelivery($partialDeliveryId, $setupValues);

        return array_key_exists('projekt', $setupValues) ? (int)$setupValues['projekt'] : 0;
    }

    /**
     * @param int $orderId
     *
     * @return array
     */
    private function getSetupValues(int $orderId): array
    {
        $select = ($this->database->select())
            ->cols(self::PARTIAL_DELIVERY_SETUP_FIELDS)
            ->from('auftrag')
            ->where('id = :order_id')
            ->getStatement();
        return $this->database->fetchRow($select, ['order_id' => $orderId]);
    }

    /**
     * @param int   $positionId
     * @param array $fields
     *
     * @return void
     */
    public function updateOrderPositionAttributes(int $positionId, array $fields): void
    {
        // these fields must not be overwritten
        $restrictedFields = [
            'id',
            'menge',
            'auftrag',
            'sort',
            'artikel',
            'webid',
            'explodiert',
            'explodiert_parent',
        ];
        foreach ($restrictedFields as $restrictedField) {
            unset($fields[$restrictedField]);
        }
        if (!isset($fields['steuersatz'])) {
            $fields['steuersatz'] = null;
        }
        $update = ($this->database->update())
            ->table('auftrag_position')
            ->cols($fields)
            ->where('id = :position_id')
            ->getStatement();
        $fields['position_id'] = $positionId;
        $this->database->perform($update, $fields);
    }
}
