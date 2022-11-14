<?php

namespace Xentral\Carrier\Dhl\Data;

class Contact
{
  public ?Communication $Communication;
  public ?NativeAddress $Address;
  public ?Name $Name;
}