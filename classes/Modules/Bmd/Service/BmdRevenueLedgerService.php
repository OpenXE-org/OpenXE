<?php

namespace Xentral\Modules\Bmd\Service;

use RuntimeException;
use Xentral\Components\Database\Database;
use Xentral\Modules\Bmd\Exception\InvalidArgumentException;

class BmdRevenueLedgerService
{
    /** @var Database $db */
    private $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * @param int    $revenueledger
     * @param string $label
     * @param string $taxcode
     * @param float  $salestaxpercent
     */
    public function saveBmdRevenueLedger($revenueledger, $label, $taxcode, $salestaxpercent)
    {
        if (empty($revenueledger)) {
            throw new InvalidArgumentException('revenueledger can not be empty');
        }

        $sql = 'INSERT INTO `bmdrevenueledger` 
                (
                    `revenueledger`,
                    `label`,
                    `taxcode`,
                    `salestaxpercent`                   
                )
                VALUES (
                    :revenueledger, 
                    :label, 
                    :taxcode, 
                    :salestaxpercent
                    )';

        $values = [
            'revenueledger'   => $revenueledger,
            'label'           => $label,
            'taxcode'         => $taxcode,
            'salestaxpercent' => $salestaxpercent,
        ];
        $this->db->perform($sql, $values);
        $insertId = $this->db->lastInsertId();
        if ($insertId === 0) {
            throw new RuntimeException('Import template could not be created.');
        }
    }

    /**
     * @param int    $id
     * @param int    $revenueledger
     * @param string $label
     * @param string $taxcode
     * @param float  $salestaxpercent
     */
    public function updateBmdRevenueLedger($id, $revenueledger, $label, $taxcode, $salestaxpercent)
    {
        if (empty($revenueledger)) {
            throw new InvalidArgumentException('revenueledger can not be empty');
        }

        $sql = 'UPDATE `bmdrevenueledger` 
                SET 
                    `revenueledger`=:revenueledger,
                    `label` = :label,
                    `taxcode` = :taxcode,
                    `salestaxpercent` = :salestaxpercent
                WHERE id=:id';

        $values = [
            'revenueledger'   => $revenueledger,
            'label'           => $label,
            'taxcode'         => $taxcode,
            'salestaxpercent' => $salestaxpercent,
            'id'              => $id,
        ];

        $numAffected = (int)$this->db->fetchAffected($sql, $values);

        if ($numAffected == 0) {
            throw new RuntimeException('Import template could not be updated.');
        }
    }

    /**
     * @param int $id
     *
     * @return array
     */
    public function getBmdRevenueLadgerById($id)
    {
        $sql = 'SELECT `id`, `revenueledger`,`label`,`taxcode`,`salestaxpercent` FROM `bmdrevenueledger` WHERE id =:id';
        $values = [
            'id' => $id,
        ];
        $data = $this->db->fetchRow($sql, $values);
        if (empty($data)) {
            throw new InvalidArgumentException('Id is not valid.');
        }

        return $data;
    }

    /**
     * @param int $id
     */
    public function deleteRevenueLedgerById($id)
    {
        $sql = "DELETE FROM `bmdrevenueledger` WHERE `id` =:id";
        $numAffected = (int)$this->db->fetchAffected($sql, ['id' => $id]);

        if ($numAffected == 0) {
            throw new RuntimeException('Import template could not be deleted.');
        }
    }

    /**
     * @return array
     */
    public function getAllRevenueLedgers()
    {
        $sql = 'SELECT `id`, `revenueledger`,`label`,`taxcode`,`salestaxpercent` FROM `bmdrevenueledger`';

        return $this->db->fetchAll($sql);
    }
}
