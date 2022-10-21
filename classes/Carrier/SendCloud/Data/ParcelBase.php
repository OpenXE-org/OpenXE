<?php

namespace Xentral\Carrier\SendCloud\Data;

abstract class ParcelBase
{
  public ?string $Name = null;
  public ?string $CompanyName = null;
  public ?string $Address = null;
  public ?string $Address2 = null;
  public ?string $HouseNumber = null;
  public ?string $City = null;
  public ?string $PostalCode = null;
  public ?string $Telephone = null;
  public bool $RequestLabel = true;
  public ?string $EMail = null;
  public ?string $Country = null;
  public ?int $ShippingMethodId = null;
  /**
   * @var ?int weight in grams
   */
  public ?int $Weight = null;
  public ?string $OrderNumber = null;
  public ?string $TotalOrderValueCurrency = null;
  public ?float $TotalOrderValue = null;
  public ?string $CountryState = null;
  public ?string $CustomsInvoiceNr = null;
  public ?int $CustomsShipmentType = null;
  public ?string $ExternalReference = null;
  public ?int $TotalInsuredValue = null;
  public ?array $ParcelItems = array();
  public bool $IsReturn = false;
  public ?string $Length = null;
  public ?string $Width = null;
  public ?string $Height = null;
}