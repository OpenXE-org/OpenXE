<?php

namespace Xentral\Modules\Country\Data;

use Xentral\Modules\Country\Exception\CountryInvalidArgumentException;

class CountryDataValidator
{
    /**
     * @param $isoAlpha3
     */
    public function ensureIso3($isoAlpha3)
    {
        if (strlen($isoAlpha3) !== 3) {
            throw new CountryInvalidArgumentException('ISO-3166-Alpha3-Feld ist nicht 3 Zeichen lang');
        }
    }

    /**
     * @param $isoAlpha2
     */
    public function ensureIso2($isoAlpha2)
    {
        if (strlen($isoAlpha2) !== 2) {
            throw new CountryInvalidArgumentException('ISO-3166-Alpha2-Feld ist nicht 2 Zeichen lang');
        }
    }

    /**
     * @param $isoNumeric
     */
    public function ensureIsoNumeric($isoNumeric)
    {
        if (strlen($isoNumeric) !== 3) {
            throw new CountryInvalidArgumentException(
                'Numerischer Ländercode (ISO-3166 numeric) ist nicht 3 Zeichen lang'
            );
        }
    }

    /**
     * @param $nameGerman
     */
    public function ensureNameGerman($nameGerman)
    {
        if (trim($nameGerman) === '') {
            throw new CountryInvalidArgumentException('Deutsche Bezeichnung ist leer');
        }
    }

    /**
     * @param $nameEnglish
     */
    public function ensureNameEnglish($nameEnglish)
    {
        if (trim($nameEnglish) === '') {
            throw new CountryInvalidArgumentException('Englische Bezeichnung ist leer');
        }
    }

    /**
     * @param $isEu
     */
    public function ensureIsEu($isEu)
    {
        if (!is_bool($isEu)) {
            throw new CountryInvalidArgumentException('Fehlerhafter EU-Parameter. Es sind nur boolsche Werte erlaubt.');
        }
    }
}
