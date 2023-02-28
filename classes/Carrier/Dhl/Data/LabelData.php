<?php

/*
 * SPDX-FileCopyrightText: 2022 Andreas Palm
 *
 * SPDX-License-Identifier: AGPL-3.0-only
 */

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