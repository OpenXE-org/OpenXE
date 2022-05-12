<?php

namespace Xentral\Modules\Country\Service;

use Xentral\Components\Database\Database;
use Xentral\Modules\Country\Data\CountryData;
use Xentral\Modules\Country\Exception\CountryMigrationFailedException;

final class CountryMigrationService
{
    /** @var Database $db */
    private $db;

    /**
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->db = $database;
    }

    /**
     * Migration notwendig?
     *
     * * Prüft ob Länderliste in Datenbank vollständig
     * * Prüft bei vorhandnen Ländern ob ISO3-Code in Datenbank gefüllt
     *
     * @param string $tableName
     *
     * @return bool
     */
    public function needsMigration($tableName = 'laender')
    {
        $countries = $this->getCountryList();
        $countryCodes = array_map(function ($country) {
            /** @var CountryData $country */
            return $country->getIsoAlpha2();
        }, $countries);

        // Check for empty ISO3 codes on existing entries
        $emptyIso3Count = (int)$this->db->fetchValue(
            "SELECT COUNT(*)
             FROM {$tableName} AS l 
             WHERE l.iso IN (:country_codes) 
             AND (l.iso3 IS NULL OR l.iso3 = '')",
            ['country_codes' => $countryCodes]
        );
        if ($emptyIso3Count > 0) {
            return true;
        }

        // Check for missing countries
        $existingCount = (int)$this->db->fetchValue(
            "SELECT COUNT(l.id) FROM {$tableName} AS l WHERE l.iso IN (:country_codes)",
            ['country_codes' => $countryCodes]
        );
        if ($existingCount !== count($countries)) {
            return true;
        }

        return false;
    }

    /**
     * @param string $tableName
     *
     * @return void
     */
    public function doMigration($tableName = 'laender')
    {
        $countries = $this->getCountryList();
        $iso2List = array_map(function ($country){
            /** @var CountryData $country */
            return '(SELECT "' . $this->db->escapeValue($country->getIsoAlpha2()) . '" AS iso2_orig,
                            "' . $this->db->escapeValue($country->getIsoAlpha3()) . '" AS iso3_orig, 
                            "' . $this->db->escapeValue($country->getIsoNumeric()) . '" AS num_orig, 
                            "' . $this->db->escapeValue($country->getNameGerman()) . '" AS name_de_orig, 
                            "' . $this->db->escapeValue($country->getNameEnglish()) . '" AS name_en_orig, 
                            "' . $this->db->escapeValue((int)$country->isEu()) . '" AS is_eu_orig)';
        }, $countries);
        $sqlIsoData = implode(' UNION ', $iso2List);

        $missingValues = $this->db->fetchAll("SELECT * FROM {$tableName} AS l 
                                                    RIGHT JOIN ({$sqlIsoData}) AS iso_data 
                                                    ON l.iso=iso_data.iso2_orig
                                                    WHERE l.iso IS NULL OR l.iso = '' OR l.iso3 != iso_data.iso3_orig
                                                    ");

        //TODO check is ISO2 present multiple times

        foreach ($missingValues as $missingValue){
            if($missingValue['id'] == null){
                // whole entry missing
                $this->db->perform(
                                    "INSERT INTO {$tableName}(iso, iso3, num_code, bezeichnung_de, bezeichnung_en, eu) 
                                     VALUES (:iso2_code, :iso3_code, :num_code, :name_de, :name_en, :is_eu)",
                                    [
                                        'iso2_code' => $missingValue['iso2_orig'],
                                        'iso3_code' => $missingValue['iso3_orig'],
                                        'num_code'  => $missingValue['num_orig'],
                                        'name_de'   => $missingValue['name_de_orig'],
                                        'name_en'   => $missingValue['name_en_orig'],
                                        'is_eu'     => $missingValue['is_eu_orig'],
                                    ]
                                );
            }else{
                // entry corrupted/iso3 missing
                $this->db->perform(
                    "UPDATE {$tableName} SET iso3 = :iso3_code, num_code = :num_code WHERE iso = :iso2_code LIMIT 1",
                    [
                        'iso2_code' => $missingValue['iso2_orig'],
                        'iso3_code' => $missingValue['iso3_orig'],
                        'num_code'  => $missingValue['num_orig'],
                    ]
                );
            }
        }

    }

    /**
     * @return CountryData[]
     */
    public function getCountryList()
    {
        $countryArray = include __DIR__ . '/../migration/iso_code_data.php';

        return array_map(function ($country) {
            return new CountryData($country[0], $country[1], $country[2], $country[3], $country[4], (bool)$country[5]);
        }, $countryArray);
    }
}
