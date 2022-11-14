<?php

namespace Xentral\Carrier\Dhl\Data;

use DateTimeImmutable;

class ShipmentDetails
{
  public string $product;
  public string $accountNumber;
  public string $customerReference;
  private string $shipmentDate;
  public string $costCentre;
  public string $returnShipmentAccountNumber;
  public string $returnShipmentReference;
  public ShipmentItem $ShipmentItem;
  public ShipmentService $Service;
  public ShipmentNotification $Notification;
  public Bank $BankData;

  public function SetShipmentDate(DateTimeImmutable $date): void {
    $this->shipmentDate = $date->format('Y-m-d');
  }

  public function GetShipmentDate(): DateTimeImmutable {
    return DateTimeImmutable::createFromFormat('Y-m-d', $this->shipmentDate);
  }
}