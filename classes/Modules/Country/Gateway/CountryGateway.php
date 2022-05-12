<?php

namespace Xentral\Modules\Country\Gateway;

use Xentral\Components\Database\Database;
use Xentral\Modules\Country\Data\CountryDataValidator;

final class CountryGateway
{
    /** @var Database $db */
    private $db;

    /** @var CountryDataValidator */
    private $validator;

    /**
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->db = $database;
        $this->validator = new CountryDataValidator();
    }

    /**
     * @return array
     */
    public function findAll()
    {
        return $this->db->fetchAll('SELECT ' .
            $this->getColumnArraySQLMapping() .
            'FROM laender AS l;');
    }

    /**
     * @param string $name
     *
     * @return array|null
     */
    public function findByName($name)
    {
        $this->validator->ensureNameGerman($name);
        $sql = 'SELECT ' .
            $this->getColumnArraySQLMapping() .
            'FROM laender AS l
             WHERE bezeichnung_de LIKE :name OR bezeichnung_en LIKE :name;';

        return $this->db->fetchRow($sql, ['name' => $name]);
    }

    /**
     * @param string $iso2Code
     *
     * @return array|null
     */
    public function findByIso2Code($iso2Code)
    {
        $this->validator->ensureIso2($iso2Code);
        $result = $this->db->fetchRow(
            'SELECT ' .
            $this->getColumnArraySQLMapping() .
            'FROM laender AS l
             WHERE iso = :iso2_code;',
            ['iso2_code' => $iso2Code]
        );

        // TODO throw Exception
        return $result;
    }

    /**
     * @param string $iso3Code
     *
     * @return array|null
     */
    public function findByIso3Code($iso3Code)
    {
        $this->validator->ensureIso3($iso3Code);
        $result = $this->db->fetchRow(
            'SELECT ' .
            $this->getColumnArraySQLMapping() .
            'FROM laender AS l
             WHERE iso3 = :iso3_code;',
            ['iso3_code' => $iso3Code]
        );

        // TODO throw Exception
        return $result;
    }

    /**
     * @param string $numericCode
     *
     * @return array|null
     */
    public function findByNumericCode($numericCode)
    {
        $this->validator->ensureIsoNumeric($numericCode);
        $result = $this->db->fetchRow(
            'SELECT ' .
            $this->getColumnArraySQLMapping() .
            'FROM laender AS l
             WHERE num_code = :num_code;',
            ['num_code' => $numericCode]
        );

        // TODO throw Exception
        return $result;
    }

    /**
     * Returns sql-ready 'column AS arrayName' structure
     *
     * @return string
     */
    private function getColumnArraySQLMapping()
    {
        return
            'l.iso AS iso2_code,
             l.iso3 AS iso3_code,
             l.num_code AS num_code,
             l.bezeichnung_de AS name_de,
             l.bezeichnung_en AS name_en,
             l.eu AS is_eu ';
    }
}
