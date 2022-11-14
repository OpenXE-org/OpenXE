<?php

namespace Xentral\Carrier\Dhl\Data;

class LabelData
{
  public Statusinformation $Status;
  public ?string $shipmentNumber;
  public ?string $labelUrl;
  public ?string $labelData;
  public ?string $returnLabelUrl;
  public ?string $returnLabelData;
  public ?string $exportLabelUrl;
  public ?string $exportLabelData;
  public ?string $codLabelUrl;
  public ?string $codLabelData;
}