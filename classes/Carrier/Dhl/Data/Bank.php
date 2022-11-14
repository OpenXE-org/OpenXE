<?php

namespace Xentral\Carrier\Dhl\Data;

class Bank
{
  public string $accountOwner;
  public string $bankName;
  public string $iban;
  public string $note1;
  public string $note2;
  public ?string $bic;
  public ?string $accountreference;
}