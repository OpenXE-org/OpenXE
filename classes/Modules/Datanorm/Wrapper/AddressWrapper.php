<?php

namespace Xentral\Modules\Datanorm\Wrapper;

use erpAPI;
use Xentral\Components\Database\Database;
use Xentral\Modules\Datanorm\Data\DatanormVTypeData;

class AddressWrapper
{
    /** @var erpAPI $erp */
    private $erp;

    /** @var Database */
    private $db;

    /**
     * @param Database $db
     * @param erpAPI   $erp
     */
    public function __construct(Database $db, erpAPI $erp)
    {
        $this->db = $db;
        $this->erp = $erp;
    }

    /**
     * @param DatanormVTypeData $supplierData
     *
     * @return int
     */
    public function insertSupplierIfNotExists(DatanormVTypeData $supplierData): int
    {
        $supplierName =
            trim(
                $supplierData->getAdress1() . ' ' .
                $supplierData->getAdress2() . ' ' .
                $supplierData->getAdress3()
            );

        if (empty($supplierName)) {
            $supplierName = $supplierData->getDescription();
        }

        $supplierId = $this->findAdressByName($supplierName);

        if (empty($supplierId)) {
            $sql =
                'INSERT INTO `adresse`
                (`typ`,`name`,`ort`,`strasse`,`plz`,`land`,`sonstiges`,`lieferantennummer`) VALUES
                (:type, :name , :city, :street, :zip, :country, :miscellaneous, :supplier_number)
            ';

            $this->db->perform(
                $sql,
                [
                    'type'            => 'firma',
                    'name'            => $supplierName,
                    'city'            => $supplierData->getCity(),
                    'street'          => $supplierData->getStreet(),
                    'zip'             => $supplierData->getZip(),
                    'country'         => $this->mapCountryId($supplierData->getCountryId()),
                    'miscellaneous'   => $supplierData->getProducerToken(),
                    'supplier_number' => $this->erp->GetNextLieferantennummer(),
                    'sonstiges'       => $supplierData->getDescription(),
                ]
            );

            $supplierId = $this->db->lastInsertId();
        }

        return $supplierId;
    }

    /**
     * @param string $name
     *
     * @return int
     */
    private function findAdressByName(string $name)
    {
        $select = $this->db->select()
            ->cols(['id'])
            ->from('adresse')
            ->where('name=?', $name)
            ->limit(1);

        $result = $this->db->fetchCol(
            $select->getStatement(),
            $select->getBindValues()
        );

        if (!empty($result)) {
            return $result[0];
        }

        return 0;
    }

    /**
     * TODO Need more examples
     *
     * @param string $countryId
     *
     * @return string
     */
    private function mapCountryId(string $countryId)
    {
        $mapping = [
            'D' => 'DE',
        ];
        if (array_key_exists($countryId, $mapping)) {
            return $mapping[$countryId];
        }

        return $countryId;
    }
}
