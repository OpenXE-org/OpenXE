<?php
namespace Xentral\Carrier\SendCloud\Data;

class ShippingProduct {
    public string $Name;
    public string $Carrier;
    public string $ServicePointsCarrier;
    public string $Code;
    public int $MinWeight;
    public int $MaxWeight;
    public array $ShippingMethods;

    public static function fromApiResponse(object $data): ShippingProduct {
        $obj = new ShippingProduct();
        $obj->Name = $data->name;
        $obj->Carrier = $data->carrier;
        $obj->ServicePointsCarrier = $data->service_points_carrier;
        $obj->Code = $data->code;
        $obj->MinWeight = $data->weight_range->min_weight;
        $obj->MaxWeight = $data->weight_range->max_weight;
        foreach ($data->methods as $method) {
            $child = ShippingMethod::fromApiResponse($method);
            $child->Carrier = $obj->Carrier;
            $obj->ShippingMethods[] = $child;
        }
        return $obj;
    }
}