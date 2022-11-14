<?php

namespace Xentral\Carrier\Dhl\Data;

class Shipment
{
  public ShipmentDetails $ShipmentDetails;
  public Shipper $Shipper;
  public string $ShipperReference;
  public Receiver $Receiver;
  public ?Shipper $ReturnReceiver;
  public ?ExportDocument $ExportDocument;
  public ?string $feederSystem;

  public function __construct()
  {
    $this->ShipmentDetails = new ShipmentDetails();
    $this->Shipper = new Shipper();
    $this->ShipperReference = '';
    $this->Receiver = new Receiver();
  }
}