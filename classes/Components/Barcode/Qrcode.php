<?php

namespace Xentral\Components\Barcode;

final class Qrcode
{
    /** @var string TYPE_QRCODE */
    const TYPE_DEFAULT = 'QRCODE';

    /** @var string TYPE_EC_LOW Low error correction */
    const TYPE_EC_LOW = 'QRCODE,L';

    /** @var string TYPE_EC_MEDIUM Medium error correction */
    const TYPE_EC_MEDIUM = 'QRCODE,M';

    /** @var string TYPE_EC_QUARTILE Better error correction */
    const TYPE_EC_QUARTILE = 'QRCODE,Q';

    /** @var string TYPE_EC_HIGH Best error correction */
    const TYPE_EC_HIGH = 'QRCODE,H';

    /** @var array $validTypes */
    public static $validTypes = [
        self::TYPE_DEFAULT,
        self::TYPE_EC_LOW,
        self::TYPE_EC_MEDIUM,
        self::TYPE_EC_QUARTILE,
        self::TYPE_EC_HIGH,
    ];

    /** @var TcpdfBarcode2d $barcode */
    private $barcode;

    /**
     * @param TcpdfBarcode2d $barcode
     */
    public function __construct(TcpdfBarcode2d $barcode)
    {
        $this->barcode = $barcode;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->barcode->getText();
    }

    /**
     * Returns the QR code as array representation
     *
     * @return array
     */
    public function toArray()
    {
        return $this->barcode->getBarcodeArray();
    }

    /**
     * Returns the QR code as HTML representation
     *
     * @param int    $width  Width of a single rectangle element in pixels.
     * @param int    $height Height of a single rectangle element in pixels.
     * @param string $color  Foreground color for bar elements (background is transparent).
     *
     * @return string HTML code
     */
    public function toHtml($width = 10, $height = 10, $color = 'black')
    {
        return $this->barcode->getBarcodeHtml($width, $height, $color);
    }

    /**
     * Returns the QR code as SVG document
     *
     * @param int    $width  Width of a single rectangle element in user units
     * @param int    $height Height of a single rectangle element in user units
     * @param string $color  Foreground color (in SVG format) for bar elements (background is transparent)
     *
     * @return string SVG document
     */
    public function toSvg($width = 10, $height = 10, $color = 'black')
    {
        return $this->barcode->getBarcodeSvg($width, $height, $color);
    }

    /**
     * Returns the QR code as PNG image (requires GD or Imagick library)
     *
     * @param int   $width  Width of a single rectangle element in pixels
     * @param int   $height Height of a single rectangle element in pixels
     * @param array $color  RGB-Array (0-255) foreground color for bar elements (background is transparent)
     *
     * @return string Image as string
     */
    public function toPng($width = 10, $height = 10, $color = [0, 0, 0])
    {
        return $this->barcode->getBarcodePng($width, $height, $color);
    }

}
