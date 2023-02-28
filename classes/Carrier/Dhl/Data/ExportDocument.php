<?php

/*
 * SPDX-FileCopyrightText: 2022 Andreas Palm
 *
 * SPDX-License-Identifier: AGPL-3.0-only
 */

namespace Xentral\Carrier\Dhl\Data;

class ExportDocument
{
  const TYPE_PRESENT = 'PRESENT';
  const TYPE_DOCUMENT = 'DOCUMENT';
  const TYPE_COMMERCIAL_GOODS = 'COMMERCIAL_GOODS';
  const TYPE_COMMERCIAL_SAMPLE = 'COMMERCIAL_SAMPLE';
  const TYPE_RETURN_OF_GOODS= 'RETURN_OF_GOODS';
  const TYPE_OTHER = 'OTHER';

  const TERMS_DDP = 'DDP';
  const TERMS_DXV = 'DXV';
  const TERMS_DAP = 'DAP';
  const TERMS_DDX = 'DDX';
  const TERMS_CPT = 'CPT';

  public ?string $invoiceNumber;
  public string $exportType;
  public ?string $exportTypeDescription;
  public ?string $termsOfTrade;
  public string $placeOfCommital;
  public ?float $additionalFee;
  public ?string $customsCurrency;
  public ?string $permitNumber;
  public ?string $attestationNumber;
  public ?string $addresseesCustomsReference;
  public ?string $sendersCustomsReference;
  public ?bool $WithElectronicExportNtfctn;
  /** @var ExportDocPosition[] $ExportDocPosition */
  public array $ExportDocPosition;
}