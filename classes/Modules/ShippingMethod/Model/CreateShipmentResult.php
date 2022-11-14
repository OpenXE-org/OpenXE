<?php

namespace Xentral\Modules\ShippingMethod\Model;

class CreateShipmentResult
{
  public bool $Success = false;
  public array $Errors = [];
  public ?string $Label;
  public ?string $ExportDocuments;
  public ?string $TrackingNumber;
  public ?string $TrackingUrl;
}