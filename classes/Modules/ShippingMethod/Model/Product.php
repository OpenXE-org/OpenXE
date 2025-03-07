<?php

/*
 * SPDX-FileCopyrightText: 2022 Andreas Palm
 *
 * SPDX-License-Identifier: LicenseRef-EGPL-3.1
 */

namespace Xentral\Modules\ShippingMethod\Model;

class Product
{
  public string $Id;
  public string $Name;
  public float $LengthMin = 0;
  public float $LengthMax = 500;
  public float $WidthMin = 0;
  public float $WidthMax = 500;
  public float $HeightMin = 0;
  public float $HeightMax = 500;
  public float $WeightMin = 0;
  public float $WeightMax = 100;
    /**
     * @var ShipmentService[]
     */
  public array $AvailableServices = [];

  public static function Create(string $id, string $name):Product {
    $obj = new Product();
    $obj->Id = $id;
    $obj->Name = $name;
    return $obj;
  }

  public function WithLength(float $min, float $max): Product {
    $this->LengthMin = $min;
    $this->LengthMax = $max;
    return $this;
  }

  public function WithWidth(float $min, float $max): Product {
    $this->WidthMin = $min;
    $this->WidthMax = $max;
    return $this;
  }

  public function WithHeight(float $min, float $max): Product {
    $this->HeightMin = $min;
    $this->HeightMax = $max;
    return $this;
  }

  public function WithWeight(float $min, float $max): Product {
    $this->WeightMin = $min;
    $this->WeightMax = $max;
    return $this;
  }

    /**
     * @param ShipmentService[] $services
     * @return $this
     */
  public function WithServices(array $services): Product {
    $this->AvailableServices = array_merge($this->AvailableServices, $services);
    return $this;
  }
}