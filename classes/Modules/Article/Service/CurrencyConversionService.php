<?php


namespace Xentral\Modules\Article\Service;


use Xentral\Modules\Article\Exception\CurrencyExchangeRateNotFoundException;

class CurrencyConversionService
{
    /**
     * @deprecated
     * @var \erpAPI
     */
    private $erp;

    /**
     * @var array
     */
    private $cache;

    /**
     * CurrencyConversionService constructor.
     *
     * @param \erpAPI $erp
     */
    public function __construct(\erpAPI $erp)
    {
        $this->erp = $erp;
        $this->cache = [];
    }

    /**
     * @param string $currencyCode
     *
     * @throws CurrencyExchangeRateNotFoundException
     *
     * @return float
     */
    public function tryGetEuroExchangeRateFromCurrencyCode($currencyCode)
    {
        if(isset($this->cache[$currencyCode])) {
            $exchangeRate = $this->cache[$currencyCode];
        }
        else {
            $exchangeRate = $this->erp->GetWaehrungUmrechnungskurs('EUR', $currencyCode, true);
            $this->cache[$currencyCode] = $exchangeRate;
        }
        if ($exchangeRate === false) {
            throw new CurrencyExchangeRateNotFoundException('Currency exchange rate not found: ' . $currencyCode);
        }

        return (float)$exchangeRate;
    }
}
