<?php

namespace Xentral\Modules\Country\Service;

use Xentral\Components\Database\Database;
use Xentral\Modules\Country\Data\CountryData;
use Xentral\Modules\Country\Exception\CountryNotFoundException;
use Xentral\Modules\Country\Gateway\CountryGateway;

final class CountryService
{
    /** @var CountryGateway $gateway */
    private $gateway;

    /** @var Database $db */
    private $db;

    /**
     * @param CountryGateway $gateway
     * @param Database       $database
     */
    public function __construct(CountryGateway $gateway, Database $database)
    {
        $this->gateway = $gateway;
        $this->db = $database;
    }

    /**
     * @param string $iso3Code
     *
     * @return CountryData
     */
    public function getByIso3Code($iso3Code)
    {
        $state = $this->gateway->findByIso3Code($iso3Code);
        if (empty($state)) {
            throw new CountryNotFoundException("ISO3-Code '{$iso3Code}' nicht gefunden");
        }

        return CountryData::fromState($state);

        // @todo Exception werfen wenn Gateway kein Ergebnis liefert
        // @todo Exception werfen wenn Daten unvollständig => An entsprechender Stelle Exception abfangen und
        //       in Oberflächen-Fehlermeldung umwandeln
    }

    /**
     * @param string $iso2Code
     *
     * @return CountryData
     */
    public function getByIso2Code($iso2Code)
    {
        $state = $this->gateway->findByIso2Code($iso2Code);
        if (empty($state)) {
            throw new CountryNotFoundException("ISO2-Code '{$iso2Code}' nicht gefunden");
        }

        return CountryData::fromState($state);

        // @todo Exception werfen wenn Gateway kein Ergebnis liefert
        // @todo Exception werfen wenn Daten unvollständig => An entsprechender Stelle Exception abfangen und
        //       in Oberflächen-Fehlermeldung umwandeln
    }


    /**
     * @param string $name
     *
     * @return CountryData
     */
    public function getByName($name)
    {
        $state = $this->gateway->findByName($name);
        if (empty($state)) {
            throw new CountryNotFoundException("Name '{$name}' nicht gefunden");
        }

        return CountryData::fromState($state);

        // @todo Exception werfen wenn Gateway kein Ergebnis liefert
        // @todo Exception werfen wenn Daten unvollständig => An entsprechender Stelle Exception abfangen und
        //       in Oberflächen-Fehlermeldung umwandeln
    }

    /**
     * @param string $numericCode
     *
     * @return CountryData
     */
    public function getByNumericCode($numericCode)
    {
        $state = $this->gateway->findByNumericCode($numericCode);
        if (empty($state)) {
            throw new CountryNotFoundException("Code '{$numericCode}' nicht gefunden");
        }

        return CountryData::fromState($state);

        // @todo Exception werfen wenn Gateway kein Ergebnis liefert
        // @todo Exception werfen wenn Daten unvollständig => An entsprechender Stelle Exception abfangen und
        //       in Oberflächen-Fehlermeldung umwandeln
    }

    /**
     * @param CountryData $country
     *
     * @return void
     */
    public function save(CountryData $country, $tableName = 'laender')
    {
        $sql = "SELECT COUNT(*) FROM {$tableName} WHERE iso = :iso2_code";
        $matches = $this->db->fetchValue($sql, ['iso2_code' => $country->getIsoAlpha2()]);
        if ($matches > 0) {
            $sql =
                "UPDATE {$tableName} SET
                iso3 = :iso3_code,
                bezeichnung_de = :name_de,
                bezeichnung_en = :name_en,
                eu = :is_eu,
                num_code = :num_code
                WHERE 
                iso=:iso2_code;";
            $this->db->perform(
                $sql,
                [
                    'iso2_code' => $country->getIsoAlpha2(),
                    'iso3_code' => $country->getIsoAlpha3(),
                    'num_code'  => $country->getIsoNumeric(),
                    'name_de'   => $country->getNameGerman(),
                    'name_en'   => $country->getNameEnglish(),
                    'is_eu'     => $country->isEu(),
                ]
            );

            return;
        }

        $sql =
            "INSERT INTO {$tableName}
            (iso, iso3, num_code, bezeichnung_de, bezeichnung_en, eu)
            VALUES
            (:iso2_code, :iso3_code, :num_code, :name_de, :name_en, :is_eu);";
        $this->db->perform(
            $sql,
            [
                'iso2_code' => $country->getIsoAlpha2(),
                'iso3_code' => $country->getIsoAlpha3(),
                'num_code'  => $country->getIsoNumeric(),
                'name_de'   => $country->getNameGerman(),
                'name_en'   => $country->getNameEnglish(),
                'is_eu'     => $country->isEu(),
            ]
        );
    }
}
