<?php

namespace Xentral\Carrier\SendCloud\Data;

/**
 * Label object for a parcel
 */
class Label
{
    public string $labelPrinter;
    /** @var string[] $normalPrinter */
    public array $normalPrinter;
}