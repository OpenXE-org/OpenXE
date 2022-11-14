<?php

namespace Xentral\Carrier\Dhl\Data;

class Customer
{
  public Name $Name;
  public ?string $vatID;
  public string $EKP;
  public NativeAddress $Address;
  public Contact $Contact;
  public ?Bank $Bank;
  public ?string $note;
}