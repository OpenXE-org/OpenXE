<?php

namespace Xentral\Carrier\Dhl\Data;

class Dimension
{
  public int $length;
  public int $width;
  public int $height;
  public ?string $unit;
}