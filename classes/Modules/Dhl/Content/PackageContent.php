<?php

namespace Xentral\Modules\Dhl\Content;

use Xentral\Modules\Dhl\Exception\ContentsDataException;

/**
 * Class PackageContent
 *
 * @package Xentral\Modules\Dhl\Content
 */
class PackageContent
{
    /** @var int */
    private $amount;

    /** @var string */
    private $description;

    /** @var float */
    private $value;

    /** @var string ISO-2 */
    private $countryOfOrigin;

    /** @var string */
    private $customsTariffNumber;

    /** @var float */
    private $weightInKg;

    /**
     * PackageContent constructor.
     *
     * @param int    $amount
     * @param string $description
     * @param float  $value
     * @param string $countryOfOrigin
     * @param string $customsTariffNumber
     * @param float  $weightInKg
     */
    public function __construct($amount, $description, $value, $countryOfOrigin, $customsTariffNumber, $weightInKg)
    {
        $this->checkDescription($description);
        $this->checkAmount($description, $amount);
        $this->checkWeight($description, $weightInKg);
        $this->checkCountryOfOrigin($description, $countryOfOrigin);
        $this->checkTariffNumber($description, $customsTariffNumber);
        $this->checkValue($description, $value);

        $this->description = $description;
        $this->amount = $amount;
        $this->value = $value;
        $this->countryOfOrigin = $countryOfOrigin;
        $this->customsTariffNumber = $customsTariffNumber;
        $this->weightInKg = $weightInKg;
    }

    private function checkWeight($itemName, $weight){
        if($weight <= 0){
            throw new ContentsDataException("Falsches Gewicht von '{$itemName}'");
        }
    }

    private function checkTariffNumber($itemName, $number){
        if(empty($number)){
            throw new ContentsDataException("Falsche Zolltarifnummer von '{$itemName}'");
        }
    }

    private function checkCountryOfOrigin($itemName, $country){
        if(strlen($country) !== 2){
            throw new ContentsDataException("Herkunftsland von '{$itemName}' muss ISO-alpha-2 sein");
        }
    }

    private function checkValue($itemName, $value){
        if($value <= 0){
            throw new ContentsDataException("Falscher wert von '{$itemName}': {$value}");
        }
    }

    private function checkAmount($itemName, $amount){
        if($amount <= 0){
            throw new ContentsDataException("Falsche Menge bei position '{$itemName}': {$amount}");
        }
    }

    private function checkDescription($description){
        if(empty($description)){
            throw new ContentsDataException('Fehlende Beschreibung in Positionen');
        }
    }

    /**
     * @return float
     */
    public function getWeightInKg()
    {
        return $this->weightInKg;
    }

    /**
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return float
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getCountryOfOrigin()
    {
        return $this->countryOfOrigin;
    }

    /**
     * @return string
     */
    public function getCustomsTariffNumber()
    {
        return $this->customsTariffNumber;
    }
}
