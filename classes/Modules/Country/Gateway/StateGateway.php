<?php

declare(strict_types=1);

namespace Xentral\Modules\Country\Gateway;

use Xentral\Components\Database\Database;
use Xentral\Modules\Country\Data\CountryDataValidator;

final class StateGateway
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
     * @param string $name            state name
     * @param string $iso2CountryCode iso2 country code
     *
     * @return array
     */
    public function findByNameAndIso2CountryCode(string $name, string $iso2CountryCode): array
    {
        $this->validator->ensureNameGerman($name);
        $this->validator->ensureIso2($iso2CountryCode);

        $sql = 'SELECT ' .
            $this->getColumnArraySQLMapping() .
            'FROM `bundesstaaten` AS s
             WHERE s.bundesstaat = :name AND s.aktiv = 1 AND s.land = :country_code';

        return $this->db->fetchRow($sql, ['name' => $name, 'country_code' => $iso2CountryCode]);
    }

    /**
     * @param string $iso2Code        iso state code
     * @param string $iso2CountryCode iso2 country code
     *
     * @return array
     */
    public function findByIso2CodeAndIso2CountryCode(string $iso2Code, string $iso2CountryCode): array
    {
        $this->validator->ensureIso2($iso2CountryCode);

        return $this->db->fetchRow(
            'SELECT ' .
            $this->getColumnArraySQLMapping() .
            'FROM `bundesstaaten` AS s
             WHERE s.iso = :iso2_code AND s.aktiv = 1 AND s.land = :country_code',
            ['iso2_code' => $iso2Code, 'country_code' => $iso2CountryCode]
        );
    }

    /**
     * @param string $iso2CountryCode
     *
     * @return array
     */
    public function findAllByCountryCode(string $iso2CountryCode): array
    {
        $this->validator->ensureIso2($iso2CountryCode);

        return $this->db->fetchAll(
            'SELECT ' .
            $this->getColumnArraySQLMapping() .
            'FROM `bundesstaaten` AS s
             WHERE s.land = :iso2_code AND s.aktiv = 1',
            ['iso2_code' => $iso2CountryCode]
        );
    }

    /**
     * Returns sql-ready 'column AS arrayName' structure
     *
     * @return string
     */
    private function getColumnArraySQLMapping(): string
    {
        return
            's.iso AS iso2_code,
             s.land AS iso2_country_code,
             s.bundesstaat AS name_de ';
    }
}
