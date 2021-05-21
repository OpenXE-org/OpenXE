<?php

declare(strict_types=1);

namespace Xentral\Modules\Datanorm\Service;

use Xentral\Components\Database\Database;

final class DatanormIntermediateService
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
     * @param array $intermediateEntries
     */
    public function writeMultiple(array $intermediateEntries): void
    {
        if (!empty($intermediateEntries)) {
            $sql =
                'INSERT IGNORE INTO `datanorm_intermediate` (`fileName`, `type`, `content` ,`hash`, `doneFlag`, `nummer`,`enrich`, `user_address_id`) 
                VALUES';
            $values = [];
            $sqlParams = [];

            foreach ($intermediateEntries as $index => $entry) {
                $fileName = $entry['fileName'];
                $type = $entry['type'];
                $content = json_encode($entry['obj']);
                $doneFlag = ($type === 'V' ? 1 : 0);
                $hash = ($type === 'V' ? $fileName : md5($content));
                $nummer = $entry['nummer'];
                $enrich = $entry['enrich'];
                $userAddressId = $entry['user_address_id'];

                $values['fileName' . $index] = $fileName;
                $values['type' . $index] = $type;
                $values['content' . $index] = $content;
                $values['hash' . $index] = $hash;
                $values['doneFlag' . $index] = $doneFlag;
                $values['nummer' . $index] = $nummer;
                $values['enrich' . $index] = $enrich;
                $values['user_address_id' . $index] = $userAddressId;

                $sqlParams[] =
                    '(:fileName' . $index .
                    ',:type' . $index .
                    ',:content' . $index .
                    ',:hash' . $index .
                    ',:doneFlag' . $index .
                    ',:nummer' . $index .
                    ',:enrich' . $index .
                    ',:user_address_id' . $index . ')';
            }

            $sql .= implode(',', $sqlParams);
            $this->db->perform($sql, $values);
        }
    }

    /**
     * @param array $intermdiateIds
     * @param bool  $done
     */
    public function setMultipleDone(array $intermdiateIds, bool $done = true): void
    {
        if (!empty($intermdiateIds)) {
            $sql = 'UPDATE `datanorm_intermediate` SET `doneFlag` = :done WHERE `id` IN (';

            $values = [
                'done' => $done,
            ];

            $idStrs = [];
            for ($i = 0; $i < count($intermdiateIds); $i++) {
                $idStrs[] = ':id' . $i;
                $values['id' . $i] = $intermdiateIds[$i];
            }
            $sql .= implode(',', $idStrs);
            $sql .= ')';

            $this->db->perform($sql, $values);
        }
    }

    /**
     * @param array $enrichData
     */
    public function saveEnrichData(array $enrichData): void
    {
        foreach ($enrichData as $id => $data) {
            $sql =
                'UPDATE `datanorm_intermediate` 
                SET 
                    `content` = :content, 
                    `hash` = :hash,
                    `enrich` = :enrich
                WHERE `id` = :id';

            $values = [
                'content' => $data['content'],
                'hash'    => $data['hash'],
                'enrich'  => $data['enrich'],
                'id'      => $id,
            ];
            $this->db->perform($sql, $values);
        }
    }

    public function setTAndDTypeDone(): void
    {
        $sql =
            'UPDATE `datanorm_intermediate` 
                SET 
                    `doneFlag` = :done
                WHERE (`type` = \'T\' OR `type` = \'D\')
                AND `doneFlag` = 0';

        $values = ['done' => true];
        $this->db->perform($sql, $values);
    }

    /**
     * @param int    $vId
     * @param string $supplierNumber
     */
    public function saveSupplierToVType(int $vId, string $supplierNumber)
    {
        $sql =
            'UPDATE `datanorm_intermediate` 
            SET 
                `supplier_address_id` = 
                (
                    SELECT a.id 
                    FROM `adresse` AS `a` 
                    WHERE a.lieferantennummer = :supplier_number
                    LIMIT 1
                )
            WHERE `id` = :v_id';

        $values = ['v_id' => $vId, 'supplier_number' => $supplierNumber];
        $this->db->perform($sql, $values);
    }
}
