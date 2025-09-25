<?php

/*
 * SPDX-FileCopyrightText: 2022 Andreas Palm
 *
 * SPDX-License-Identifier: AGPL-3.0-only
 */

namespace Xentral\Carrier\SendCloud\Data;

class ParcelItem
{
  public string $HsCode;

  /**
   * @var int weight in grams
   */
  public int $Weight;
  public int $Quantity;
  public string $Description;
  public string $OriginCountry;
  public float $Price;
  public ?string $Sku = null;
  public ?string $ProductId = null;

  public function toApiRequest(): array {
    return [
      'hs_code' => $this->HsCode,
      'weight' => number_format($this->Weight / 1000, 3, '.', null),
      'quantity' => $this->Quantity,
      'description' => $this->Description,
      'value' => round($this->Price, 2),
      'origin_country' => $this->OriginCountry,
      'sku' => $this->Sku ?? '',
      'product_id' => $this->ProductId ?? '',
    ];
  }

  public static function fromApiResponse(object $data): ParcelItem
  {
    $obj = new ParcelItem();
    $obj->HsCode = $data->hs_code;
    $obj->Weight = intval(floatval($data->weight)*1000);
    $obj->Quantity = $data->quantity;
    $obj->Description = $data->description;
    $obj->Price = $data->value;
    $obj->OriginCountry = $data->origin_country;
    $obj->Sku = $data->sku;
    $obj->ProductId = $data->product_id;
    return $obj;
  }
}