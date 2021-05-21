<?php

namespace Xentral\Modules\FeeReduction\Gateway;

use Xentral\Components\Database\Database;
use Xentral\Modules\FeeReduction\Exception\InvalidArgumentException;

final class FeeReductionGateway
{
    /** @var Database */
    private $db;


    /**
     * FeeReductionGateway constructor.
     *
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * @param string $doctype
     * @param int    $positionId
     *
     * @return float
     */
    public function getSumByDoctypePosition($doctype, $positionId)
    {
        return (float)$this->db->fetchValue(
            'SELECT SUM(amount * price) 
            FROM fee_reduction 
            WHERE doctype = :doctype AND position_id = :position_id',
            ['doctype' => (String)$doctype, 'position_id' => (int)$positionId]
        );
    }

    /**
     * @param string $doctype
     * @param int    $doctypeId
     *
     * @return float
     */
    public function getSumByDoctype($doctype, $doctypeId)
    {
        return (float)$this->db->fetchValue(
            'SELECT SUM(amount * price) 
            FROM fee_reduction 
            WHERE doctype = :doctype AND doctype_id = :doctype_id',
            ['doctype' => (String)$doctype, 'doctype_id' => (int)$doctypeId]
        );
    }

    /**
     * @param string $priceType
     * @param int    $doctype
     * @param int    $doctypeId
     * @param int    $positionId
     *
     * @return array
     */
    public function getFeeByType($priceType, $doctype, $doctypeId, $positionId = 0) {
        return $this->db->fetchRow(
            'SELECT * 
            FROM fee_reduction 
            WHERE doctype = :doctype AND doctype_id = :doctype_id 
              AND position_id = :position_id AND price_type = :price_type',
            [
                'price_type'  => (String)$priceType,
                'doctype'     => (String)$doctype,
                'doctype_id'  => (int)$doctypeId,
                'position_id' => (int)$positionId,
            ]
        );
    }
}