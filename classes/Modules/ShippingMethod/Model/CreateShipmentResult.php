<?php

/*
 * SPDX-FileCopyrightText: 2022 Andreas Palm
 *
 * SPDX-License-Identifier: LicenseRef-EGPL-3.1
 */

namespace Xentral\Modules\ShippingMethod\Model;

class CreateShipmentResult
{
  public bool $Success = false;
  public array $Errors = [];
  public ?string $Label;
  public ?string $ExportDocuments;
  public ?string $TrackingNumber;
  public ?string $TrackingUrl;
  public ?string $AdditionalInfo;
}