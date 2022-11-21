<?php

namespace Xentral\Carrier\Dhl\Data;

class Receiver
{
  public string $name1;
  public ?ReceiverNativeAddress $Address;
  public ?PackStation $Packstation;
  public ?Postfiliale $Postfiliale;
  public ?Communication $Communication;
}