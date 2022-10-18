<?php

declare(strict_types=1);

namespace Xentral\Modules\SubscriptionCycle;

use DateTimeInterface;

interface SubscriptionModuleInterface
{
  public function CreateInvoice(int $address, DateTimeInterface $calculationDate = null);
  public function CreateOrder(int $address, DateTimeInterface $calculationDate = null);
  public function GetPositions(int $address, string $documentType, DateTimeInterface $calculationDate = null): array;
}
