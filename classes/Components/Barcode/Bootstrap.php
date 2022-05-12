<?php

namespace Xentral\Components\Barcode;

final class Bootstrap
{
    /**
     * @return array
     */
    public static function registerServices()
    {
        return [
            'BarcodeFactory' => 'onInitBarcodeFactory',
        ];
    }

    /**
     * @return BarcodeFactory
     */
    public static function onInitBarcodeFactory()
    {
        return new BarcodeFactory();
    }
}
