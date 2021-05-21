<?php

namespace Xentral\Modules\FeeReduction\Service;

use Xentral\Components\Database\Database;
use Xentral\Modules\FeeReduction\Exception\InvalidArgumentException;
use Xentral\Modules\FeeReduction\Gateway\FeeReductionGateway;

final class FeeReductionService
{
    /** @var Database */
    private $db;
    /** @var FeeReductionGateway*/
    private $gateway;

    /**
     * FeeReductionService constructor.
     *
     * @param Database $db
     */
    public function __construct(Database $db, FeeReductionGateway $gateway)
    {
        $this->db = $db;
        $this->gateway = $gateway;
    }

    /**
     * @param string $doctype
     * @param int    $doctypeId
     * @param int    $positionId
     * @param string $priceType
     * @param float  $amount
     * @param float  $price
     * @param string $currency
     * @param string $comment
     *
     * @return int
     */
    public function create($doctype, $doctypeId, $positionId, $priceType, $amount, $price, $currency = 'EUR', $comment = '')
    {
        if(empty($doctype)) {
            throw new InvalidArgumentException('doctype is empty');
        }
        if(empty($doctypeId)) {
            throw new InvalidArgumentException('doctype_id is empty');
        }
        if(empty($priceType)) {
            throw new InvalidArgumentException('pricetype is empty');
        }

        if($this->gateway->getFeeByType($priceType, $doctype, $doctypeId, $positionId)) {
            throw new InvalidArgumentException('pricetype allready exists');
        }

        $this->db->perform(
            'INSERT INTO fee_reduction (doctype, doctype_id, position_id, price_type, amount, price, comment, currency)
            VALUES (:doctype, :doctype_id, :position_id, :price_type, :amount, :price, :comment, :currency)',
            [
                'doctype' => $doctype,
                'doctype_id' => empty($doctypeId)?0:(int)$doctypeId,
                'position_id' => empty($positionId)?0:(int)$positionId,
                'price_type' => $priceType,
                'amount' => empty($amount)?1:(int)$amount,
                'price' => (float)$price,
                'comment' => (String)$comment,
                'currency' => (String)$currency,
            ]
        );

        return (int)$this->db->lastInsertId();
    }

}