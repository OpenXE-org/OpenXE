<?php

namespace Xentral\Modules\Country\Data;

final class CountryData
{
    /** @var string $isoAlpha2 */
    private $isoAlpha2;

    /** @var string $isoAlpha3 */
    private $isoAlpha3;

    /** @var string $isoNumeric */
    private $isoNumeric;

    /** @var string $nameGerman */
    private $nameGerman;

    /** @var string $nameEnglish */
    private $nameEnglish;

    /** @var bool $isEu */
    private $isEu;

    /**
     * @param string $isoAlpha2   ISO 3166 ALPHA-2
     * @param string $isoAlpha3   ISO 3166 ALPHA-3
     * @param string $isoNumeric  ISO 3166 numeric
     * @param string $nameGerman  German name
     * @param string $nameEnglish English name
     * @param bool   $isEu
     */
    public function __construct($isoAlpha2, $isoAlpha3, $isoNumeric, $nameGerman, $nameEnglish, $isEu)
    {
        $validator = new CountryDataValidator();
        $validator->ensureIso2($isoAlpha2);
        $validator->ensureIso3($isoAlpha3);
        if ($isoAlpha2 !== 'XK' /* Kosovo has no numeric id */) {
            $validator->ensureIsoNumeric($isoNumeric);
        }
        $validator->ensureNameGerman($nameGerman);
        $validator->ensureNameEnglish($nameEnglish);
        $validator->ensureIsEu($isEu);

        $this->isoAlpha2 = $isoAlpha2;
        $this->isoAlpha3 = $isoAlpha3;
        $this->isoNumeric = $isoNumeric;
        $this->nameGerman = $nameGerman;
        $this->nameEnglish = $nameEnglish;
        $this->isEu = $isEu;
    }

    /**
     * @param array $state
     *
     * @return CountryData
     */
    public static function fromState(array $state)
    {
        return new self(
            $state['iso2_code'],
            $state['iso3_code'],
            $state['num_code'],
            $state['name_de'],
            $state['name_en'],
            (bool)$state['is_eu']
        );
    }

    /**
     * @return string
     */
    public function getIsoAlpha2()
    {
        return $this->isoAlpha2;
    }

    /**
     * @return string
     */
    public function getIsoAlpha3()
    {
        return $this->isoAlpha3;
    }

    /**
     * @return string
     */
    public function getIsoNumeric()
    {
        return $this->isoNumeric;
    }

    /**
     * @return string
     */
    public function getNameGerman()
    {
        return $this->nameGerman;
    }

    /**
     * @return string
     */
    public function getNameEnglish()
    {
        return $this->nameEnglish;
    }

    /**
     * @return bool
     */
    public function isEu()
    {
        return $this->isEu;
    }
}
