<?php

namespace Xentral\Carrier\Dhl\Data;

class Statusinformation
{
  public int $statusCode;
  public string $statusText;
  public array|string $statusMessage;
  public string $statusType;
  public StatusElement $errorMessage;
  public StatusElement $warningMessage;
}