<?php

declare(strict_types=1);

namespace Xentral\Modules\Datanorm\Service;

use Xentral\Components\Database\Database;

final class DatanormIntermediateGateway
{

    /** @var Database */
    private $db;

    /**
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * @param int $limit
     *
     * @return array
     */
    public function getLines(int $limit): array
    {
        $sqlOnlyA =
            'SELECT d.id, d.fileName, d.type, d.content 
            FROM `datanorm_intermediate` AS `d`
            WHERE d.type = \'A\'
            AND d.errorFlag = 0
            AND d.doneFlag = 0
            AND d.ready = 1
            ORDER BY d.id DESC
            LIMIT ' . $limit;

        $sqlNotV =
            'SELECT d.id, d.fileName, d.type, d.content 
            FROM `datanorm_intermediate` AS `d`
            WHERE d.type != \'V\'
            AND d.errorFlag = 0
            AND d.doneFlag = 0
            AND d.ready = 1
            ORDER BY d.id DESC
            LIMIT ' . $limit;

        // Articles (type A) MUST be imported before anything else;
        // 'ORDER BY type' makes the query slower than 2 single querys
        $rows = $this->db->fetchAll($sqlOnlyA);

        if (empty($rows)) {
            $rows = $this->db->fetchAll($sqlNotV);
        }

        return $rows;
    }

    /**
     * @param int $limit
     *
     * @return array
     */
    public function getLinesToEnrich(int $limit): array
    {
        $sql =
            'SELECT d.id, d.fileName, d.type, d.content 
            FROM `datanorm_intermediate` AS `d`
            WHERE d.enrich = 1
            AND d.doneFlag = 0 
            ORDER BY d.id DESC
            LIMIT ' . $limit;

        return $this->db->fetchAll($sql);
    }

    /**
     * @param string $fileName
     *
     * @return array
     */
    public function getVType(string $fileName): array
    {
        $select = $this->db->select()
            ->cols(['id', 'fileName', 'type', 'content', 'supplier_address_id', 'user_address_id'])
            ->from('datanorm_intermediate')
            ->where('fileName = ?', $fileName)
            ->where('type = ?', 'V')
            ->limit(1);

        return $this->db->fetchRow(
            $select->getStatement(),
            $select->getBindValues()
        );
    }

    /**
     * @param string $articleNumber
     *
     * @return array
     */
    public function findArticleLineByNumber(string $articleNumber): array
    {
        $sql =
            'SELECT di.content
            FROM `datanorm_intermediate` AS `di`
            WHERE di.type = \'A\'
            AND di.nummer = :articleNumber
            ORDER BY di.id DESC
            LIMIT 1';

        $values = [
            'articleNumber' => $articleNumber,
        ];

        return $this->db->fetchRow($sql, $values);
    }

    /**
     * @param string $longTextBlockNumber
     *
     * @return array
     */
    public function findTTypeContentByBlocknumber(string $longTextBlockNumber): array
    {
        $sql =
            'SELECT di.content
            FROM `datanorm_intermediate` AS `di`
            WHERE di.type = \'T\'
            AND di.nummer = :longTextBlockNumber
            AND di.doneFlag = 0
            ORDER BY di.id';

        $values = [
            'longTextBlockNumber' => $longTextBlockNumber,
        ];

        return $this->db->fetchAll($sql, $values);
    }

    /**
     * @param string $articleNumber
     *
     * @return array
     */
    public function findDTypeContentByArticleNumer(string $articleNumber): array
    {
        $sql =
            'SELECT di.content
            FROM `datanorm_intermediate` AS `di`
            WHERE di.type = \'D\'
            AND di.nummer = :articleNumber
            AND di.doneFlag = 0
            ORDER BY di.id';

        $values = [
            'articleNumber' => $articleNumber,
        ];

        return $this->db->fetchAll($sql, $values);
    }

    /**
     * @param int $vId
     *
     * @return string
     */
    public function findSupplierNumberByVid(int $vId)
    {
        $sql =
            'SELECT a.lieferantennummer
            FROM `adresse` AS `a`
            LEFT JOIN `datanorm_intermediate` AS `di` ON di.supplier_address_id = a.id
            WHERE di.id = :v_id';

        $values = [
            'v_id' => $vId,
        ];

        $result = $this->db->fetchRow($sql, $values);

        if (!empty($result)) {
            return (string)$result['lieferantennummer'];
        }

        return '';
    }
}
