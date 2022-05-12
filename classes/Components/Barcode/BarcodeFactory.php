<?php

namespace Xentral\Components\Barcode;

use Xentral\Components\Barcode\Exception\InvalidArgumentException;

final class BarcodeFactory
{
    /**
     * @param string $codeText
     * @param string $ecLevel Error correction level [L|M|Q|H]
     *
     * @return Qrcode
     */
    public function createQrCode($codeText, $ecLevel = 'L')
    {
        $codeType = 'QRCODE,' . $ecLevel;
        if (!in_array($codeType, Qrcode::$validTypes, true)) {
            throw new InvalidArgumentException('Invalid error correction level: ' . $ecLevel);
        }
        $barcode2d = new TcpdfBarcode2d($codeType, $codeText);

        return new Qrcode($barcode2d);
    }
}
